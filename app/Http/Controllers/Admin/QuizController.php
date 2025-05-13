<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuizStoreRequest;
use App\Models\CorrespondancePair;
use App\Models\Formation;
use App\Models\Questions;
use App\Models\Quiz;
use App\Models\Reponse;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuizController extends Controller
{

    protected $quizeService;

    public function __construct(QuizService $quizeService)
    {
        $this->quizeService = $quizeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quiz = $this->quizeService->getAll();
        return view('admin.quizzes.index', compact('quiz'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formations = Formation::all();

        return view('admin.quizzes.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizStoreRequest $request)
    {
        $this->quizeService->create($request->validated());

        return redirect()->route('quiz.index')
            ->with('success', 'Le quiz a été créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quiz = $this->quizeService->show($id);
        $question = $quiz->questions->first();

        return view('admin.quizzes.show', compact('quiz', 'question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quiz = $this->quizeService->show($id);

        $formations = Formation::all();
        $questions = $quiz->questions;
        return view('admin.quizzes.edit', compact('quiz', 'formations', 'questions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Validation des champs
        ]);

        DB::beginTransaction();

        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->update($request->input('quiz'));

            $questionData = $request->input('questions', []);
            $questionFiles = $request->file('questions', []);

            foreach ($questionData as $index => &$questionInput) {
                $questionInput['quiz_id'] = $quiz->id;

                if (isset($questionFiles[$index]['media_file'])) {
                    $file = $questionFiles[$index]['media_file'];
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/medias'), $fileName);
                    $questionInput['media_url'] = 'uploads/medias/' . $fileName;
                }
            }
            unset($questionInput);

            foreach ($questionData as $questionInput) {
                // Logique de suppression des questions et réponses existantes
                if (!empty($questionInput['id']) && !empty($questionInput['_delete'])) {
                    $question = $quiz->questions()->find($questionInput['id']);
                    if ($question) {
                        $question->reponses()->delete();
                        if ($question->media_url && file_exists(public_path($question->media_url))) {
                            @unlink(public_path($question->media_url));
                        }
                        $question->delete();
                    }
                    continue;
                }

                // Mise à jour ou création de la question
                if (!empty($questionInput['id'])) {
                    $question = $quiz->questions()->find($questionInput['id']);
                    if ($question) {
                        if (!empty($questionInput['media_url']) && $question->media_url !== $questionInput['media_url']) {
                            if ($question->media_url && file_exists(public_path($question->media_url))) {
                                @unlink(public_path($question->media_url));
                            }
                        }
                        $question->update($questionInput);
                    }
                } else {
                    $question = $quiz->questions()->create($questionInput);
                }

                if (!$question) continue;

                $reponsesInput = $questionInput['reponses'] ?? [];
                $reponseIds = [];
                $leftItems = [];
                $rightItems = [];

                // Création ou mise à jour des réponses
                foreach ($reponsesInput as $reponseInput) {
                    if (!empty($reponseInput['id'])) {
                        $reponse = $question->reponses()->find($reponseInput['id']);
                        if ($reponse) {
                            $reponse->update([
                                'text' => $reponseInput['text'] ?? '',
                                'is_correct' => $reponseInput['is_correct'] ?? 0,
                                'position' => $reponseInput['position'] ?? null,
                                'match_pair' => $reponseInput['match_pair'] ?? null,
                                'bank_group' => $reponseInput['bank_group'] ?? null,
                                'flashcard_back' => $reponseInput['flashcard_back'] ?? null,
                            ]);
                            $reponseIds[] = $reponse->id;
                        }
                    } elseif (!empty($reponseInput['text'])) {
                        $reponse = $question->reponses()->create([
                            'text' => $reponseInput['text'],
                            'is_correct' => $reponseInput['is_correct'] ?? 0,
                            'position' => $reponseInput['position'] ?? null,
                            'match_pair' => $reponseInput['match_pair'] ?? null,
                            'bank_group' => $reponseInput['bank_group'] ?? null,
                            'flashcard_back' => $reponseInput['flashcard_back'] ?? null,
                        ]);
                        $reponseIds[] = $reponse->id;
                    }

                    if ($question->type === 'correspondance') {
                        if ($reponseInput['bank_group'] === 'left') {
                            $leftItems[] = $reponseInput;
                        } elseif ($reponseInput['bank_group'] === 'right') {
                            $rightItems[] = $reponseInput;
                        }
                    }
                }

                $question->reponses()->whereNotIn('id', $reponseIds)->delete();

                // Création des paires de correspondance
                if ($question->type === 'correspondance') {
                    foreach ($leftItems as $leftItem) {
                        $matchingRightItems = collect($rightItems)->filter(fn($item) => $item['match_pair'] === $leftItem['match_pair']);
                        foreach ($matchingRightItems as $rightItem) {
                            $existingPair = CorrespondancePair::where('question_id', $question->id)
                                ->where('left_text', $leftItem['text'])
                                ->where('right_text', $rightItem['text'])
                                ->first();

                            // Corriger la logique pour associer les IDs aux items
                            $leftItem['id'] = $question->reponses()->where('text', $leftItem['text'])->first()->id;
                            $rightItem['id'] = $question->reponses()->where('text', $rightItem['text'])->first()->id;

                            if (!$existingPair) {
                                CorrespondancePair::create([
                                    'question_id' => $question->id,
                                    'left_text' => $leftItem['text'],
                                    'right_text' => $rightItem['text'],
                                    'left_id' => $leftItem['id'],
                                    'right_id' => $rightItem['id']
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('quiz.index')->with('success', 'Quiz, questions et réponses mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }






    public function storeNewQuestion(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'text' => 'required|string',
            'question.type' => 'required|string',
            'points' => 'required|integer|min:1',
            'reponses' => 'required|array|min:1',
            'reponses.*.text' => 'required|string',
            'reponses.*.bank_group' => 'required_if:question.type,correspondance',
        ]);

        try {
            $quiz = Quiz::findOrFail($request->input('quiz_id'));

            $questionInput = [
                'quiz_id' => $quiz->id,
                'text' => $request->input('text'),
                'type' => $request->input('question')['type'],
                'explication' => $request->input('explication'),
                'astuce' => $request->input('astuce'),
                'points' => $request->input('points') ?? 1,
            ];

            if ($request->hasFile('media_file')) {
                $file = $request->file('media_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/medias'), $fileName);
                $questionInput['media_url'] = 'uploads/medias/' . $fileName;
            }

            $question = $quiz->questions()->create($questionInput);

            $leftItems = [];
            $rightItems = [];

            foreach ($request->input('reponses', []) as $reponseInput) {
                $reponse = $question->reponses()->create([
                    'text' => $reponseInput['text'],
                    'is_correct' => $reponseInput['is_correct'] ?? 0,
                    'position' => $reponseInput['position'] ?? null,
                    'match_pair' => $reponseInput['match_pair'] ?? null,
                    'bank_group' => $reponseInput['bank_group'] ?? null,
                    'flashcard_back' => $reponseInput['flashcard_back'] ?? null,
                ]);

                if ($question->type === 'correspondance') {
                    $itemData = [
                        'id' => $reponse->id, // On ajoute l'ID généré
                        'text' => $reponse->text,
                        'match_pair' => $reponse->match_pair,
                    ];

                    if ($reponseInput['bank_group'] === 'left') {
                        $leftItems[] = $itemData;
                    } elseif ($reponseInput['bank_group'] === 'right') {
                        $rightItems[] = $itemData;
                    }
                }
            }


            if ($question->type === 'correspondance') {
                foreach ($leftItems as $leftItem) {
                    $matchingRightItems = collect($rightItems)->filter(function ($rightItem) use ($leftItem) {
                        return $rightItem['match_pair'] === $leftItem['match_pair'];
                    });

                    foreach ($matchingRightItems as $rightItem) {
                        $existingPair = CorrespondancePair::where('question_id', $question->id)
                            ->where('left_text', $leftItem['text'])
                            ->where('right_text', $rightItem['text'])
                            ->first();

                        if (!$existingPair) {
                            CorrespondancePair::create([
                                'question_id' => $question->id,
                                'left_text' => $leftItem['text'],
                                'right_text' => $rightItem['text'],
                                'left_id' => $leftItem['id'],
                                'right_id' => $rightItem['id']
                            ]);
                        }
                    }
                }
            }


            return redirect()->route('quiz.edit', $quiz)->with('success', 'Nouvelle question créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création de la question : ' . $e->getMessage());
        }
    }


    public function storeAll(Request $request)
    {
        $request->validate([
            'quiz.titre' => 'required|string|max:255',
            'quiz.description' => 'nullable|string',

            'question.type' => 'required|string',
            'question.media_url' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,mp3,mp4|max:102400',

            'reponse.text' => 'required|array',
            'reponse.text.*' => 'required|string|max:1000',
            'reponse.is_correct' => 'nullable|array',
            'reponse.position' => 'nullable|array',
            'reponse.match_pair' => 'nullable|array',
            'reponse.bank_group' => 'nullable|array',
            'reponse.flashcard_back' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $quiz = Quiz::create($request->input('quiz'));

            $questionData = $request->input('question');
            $questionData['quiz_id'] = $quiz->id;

            if ($request->hasFile('question.media_url')) {
                $file = $request->file('question.media_url');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/medias'), $fileName);
                $questionData['media_url'] = 'uploads/medias/' . $fileName;
            }

            $question = Questions::create($questionData);

            $reponses = $request->input('reponse');

            $leftItems = [];
            $rightItems = [];

            foreach ($reponses['text'] as $index => $text) {
                $reponse = Reponse::create([
                    'question_id' => $question->id,
                    'text' => $text,
                    'is_correct' => $reponses['is_correct'][$index] ?? null,
                    'position' => $reponses['position'][$index] ?? null,
                    'match_pair' => $reponses['match_pair'][$index] ?? null,
                    'bank_group' => $reponses['bank_group'][$index] ?? null,
                    'flashcard_back' => $reponses['flashcard_back'][$index] ?? null,
                ]);

                if ($questionData['type'] === 'correspondance') {
                    if (isset($reponses['bank_group'][$index])) {
                        if ($reponses['bank_group'][$index] === 'left') {
                            $leftItems[] = [
                                'text' => $text,
                                'match_pair' => $reponses['match_pair'][$index] ?? null
                            ];
                        } elseif ($reponses['bank_group'][$index] === 'right') {
                            $rightItems[] = [
                                'text' => $text,
                                'match_pair' => $reponses['match_pair'][$index] ?? null
                            ];
                        }
                    }
                }
            }

            // Création des paires de correspondance
            if ($questionData['type'] === 'correspondance') {
                foreach ($leftItems as $leftItem) {
                    $rightItem = collect($rightItems)->firstWhere('match_pair', $leftItem['match_pair']);
                    if ($rightItem) {
                        // Vérifier si la paire existe déjà
                        $existingPair = CorrespondancePair::where('question_id', $question->id)
                            ->where('left_text', $leftItem['text'])
                            ->where('right_text', $rightItem['text'])
                            ->first();

                        if (!$existingPair) {
                            CorrespondancePair::create([
                                'question_id' => $question->id,
                                'left_text' => $leftItem['text'],
                                'right_text' => $rightItem['text'],
                                'left_id' => $leftItem['id'],
                                'right_id' => $rightItem['id']

                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('quiz.index')->with('success', 'Quiz, question et réponses créés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }



    public function import(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Réindexe les lignes pour garantir un index numérique
            $rows = array_values($rows);

            // Ignore la première ligne si elle contient les en-têtes
            $firstRow = $rows[0];
            if (strtoupper(trim($firstRow['E'])) === 'FORMATION') {
                $firstRow = $rows[1];
                $startIndex = 2;
            } else {
                $startIndex = 1;
            }

            $niveau = $firstRow['A'];
            $duree = $firstRow['B'];
            $nbPoints = $firstRow['C'];
            $titreQuiz = $firstRow['D'];
            $formationNom = trim($firstRow['E']);

            // 🔍 Recherche de la formation par titre
            $formation = Formation::where('titre', $formationNom)->first();
            if (!$formation) {
                return back()->with('error', "La formation '$formationNom' n'existe pas dans la base.");
            }

            // 📝 Création du quiz
            $quiz = Quiz::create([
                'titre' => $titreQuiz,
                'niveau' => $niveau,
                'duree' => $duree,
                'nb_points_total' => $nbPoints,
                'formation_id' => $formation->id,
            ]);

            // 📥 Import des questions
            foreach ($rows as $index => $row) {
                if ($index < $startIndex)
                    continue;

                $questionText = $row['G'] ?? null;
                if (!$questionText)
                    continue;

                $repA = $row['H'] ?? '';
                $repB = $row['I'] ?? '';
                $repC = $row['J'] ?? '';
                $bonnesLettres = strtoupper(trim($row['K'] ?? ''));
                $type = $row['L'] ?? '';

                $question = Questions::create([
                    'quiz_id' => $quiz->id,
                    'text' => $questionText,
                    'points' => 1,
                    'type' => $type,
                ]);

                $reponses = [
                    'A' => $repA,
                    'B' => $repB,
                    'C' => $repC,
                ];

                $bonnes = array_map('trim', explode(',', $bonnesLettres));
                $correctIds = [];

                foreach ($reponses as $lettre => $texte) {
                    if (empty($texte))
                        continue;

                    $reponse = Reponse::create([
                        'question_id' => $question->id,
                        'text' => $texte,
                        'is_correct' => in_array($lettre, $bonnes),
                        'position' => 1,
                    ]);

                    if (in_array($lettre, $bonnes)) {
                        $correctIds[] = $reponse->id;
                    }
                }

                // Met à jour la question avec les ID des bonnes réponses
                $question->update([
                    'correct_reponses_ids' => json_encode($correctIds)
                ]);
            }

            return back()->with('success', 'Importation réussie.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function importQuestionReponseForQuiz(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'file' => 'required|file|mimes:xlsx,xls'
        ]);


        try {
            $quiz = Quiz::find($request->quiz_id);

            if (!$quiz) {
                return back()->with('error', 'Quiz introuvable.');
            }


            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Réindexation
            $rows = array_values($rows);

            // Supposons que la première ligne contient les en-têtes
            $startIndex = 1;

            foreach ($rows as $index => $row) {
                if ($index < $startIndex) continue;

                $questionText = $row['B'] ?? null;
                if (!$questionText) continue;

                $repA = $row['C'] ?? '';
                $repB = $row['D'] ?? '';
                $repC = $row['E'] ?? '';
                $bonnesLettres = strtoupper(trim($row['F'] ?? ''));

                $question = Questions::create([
                    'quiz_id' => $quiz->id,
                    'text' => $questionText,
                    'points' => 1,
                    'type' => 'correspondance',
                ]);

                $reponses = [
                    'A' => $repA,
                    'B' => $repB,
                    'C' => $repC,
                ];

                $bonnes = array_map('trim', explode(',', $bonnesLettres));
                $correctIds = [];

                foreach ($reponses as $lettre => $texte) {
                    if (empty($texte)) continue;

                    $reponse = Reponse::create([
                        'question_id' => $question->id,
                        'text' => $texte,
                        'is_correct' => in_array($lettre, $bonnes),
                        'position' => 1,
                    ]);

                    if (in_array($lettre, $bonnes)) {
                        $correctIds[] = $reponse->id;
                    }
                }

                $question->update([
                    'correct_reponses_ids' => json_encode($correctIds)
                ]);
            }

            return back()->with('success', 'Questions et réponses importées avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation des questions', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Erreur : ' . $e->getMessage() . ' à la ligne ' . $e->getLine());
        }
    }

    /**
     * Dupliquer un quiz avec ses questions et réponses
     */
    public function duplicate($id)
    {
        DB::beginTransaction();
        try {
            $quiz = Quiz::with('questions.reponses')->findOrFail($id);
            // Dupliquer le quiz (sauf id, timestamps)
            $newQuiz = $quiz->replicate(['id', 'created_at', 'updated_at']);
            $newQuiz->titre = $quiz->titre . ' (copie)';
            $newQuiz->push();

            foreach ($quiz->questions as $question) {
                $newQuestion = $question->replicate(['id', 'quiz_id', 'created_at', 'updated_at']);
                $newQuestion->quiz_id = $newQuiz->id;
                $newQuestion->push();

                foreach ($question->reponses as $reponse) {
                    $newReponse = $reponse->replicate(['id', 'question_id', 'created_at', 'updated_at']);
                    $newReponse->question_id = $newQuestion->id;
                    $newReponse->push();
                }
            }
            DB::commit();
            return redirect()->route('quiz.edit', $newQuiz->id)
                ->with('success', 'Quiz dupliqué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }
}
