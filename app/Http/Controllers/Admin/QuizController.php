<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuizStoreRequest;
use App\Mail\NewQuizNotification;
use App\Models\CatalogueFormation;
use App\Models\CorrespondancePair;
use App\Models\Formation;
use App\Models\Questions;
use App\Models\Quiz;
use App\Models\Reponse;
use App\Services\QuizService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\QuizParticipationAnswer;
use App\Models\QuizParticipation;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Mail;

class QuizController extends Controller
{

    protected $quizeService;
    protected $notificationService;

    public function __construct(QuizService $quizeService, NotificationService $notificationService)
    {
        $this->quizeService = $quizeService;
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quiz = $this->quizeService->getAll();
        $formations = Formation::all();
        $categories = Formation::distinct()->pluck('categorie');
        $niveaux = Quiz::distinct()->pluck('niveau');
        $status = Quiz::distinct()->pluck('status');
        return view('admin.quizzes.index', compact('quiz', 'formations', 'categories', 'niveaux', 'status'));
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
        $quiz = $this->quizeService->create($request->validated());

        // Envoyer une notification pour le nouveau quiz
        $this->notificationService->notifyQuizAvailable(
            $quiz->titre,
            $quiz->id
        );

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
            // Envoyer une notification pour le nouveau quiz
            $this->notificationService->notifyQuizAvailable(
                $quiz->titre,
                $quiz->id
            );

            // Envoyer les emails aux stagiaires
            $this->sendQuizNotificationToTrainees($quiz);

            return redirect()->route('quiz.index')->with('success', 'Quiz, question et réponses créés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    protected function sendQuizNotificationToTrainees(Quiz $quiz)
    {
        // Récupérer tous les stagiaires avec des catalogues de formation
        $stagiaires = Stagiaire::whereHas('catalogue_formations')->with('user')->get();

        foreach ($stagiaires as $stagiaire) {
            if ($stagiaire->user && $stagiaire->user->email) {
                Mail::to($stagiaire->user->email)->send(new NewQuizNotification($quiz));
            }
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

            // [Code de vérification des en-têtes inchangé...]

            // Récupération des données jusqu'à la colonne L seulement
            $rows = $sheet->rangeToArray('A1:L' . $sheet->getHighestRow(), null, true, true, false);
            $rows = array_values($rows);

            // Suppression des lignes totalement vides
            $rows = array_filter($rows, function ($row) {
                return !empty(array_filter($row, function ($value) {
                    return $value !== null && trim($value) !== '';
                }));
            });

            if (count($rows) < 2) {
                return back()->with('error', 'Le fichier ne contient pas de données valides.');
            }

            $firstDataRow = $rows[1]; // La première ligne de données (après l'en-tête)
            $startIndex = 1; // On commence à l'index 1 car nous avons filtré les lignes vides

            $niveau = $firstDataRow[0] ?? null;
            $duree = $firstDataRow[1] ?? null;
            $nbPoints = $firstDataRow[2] ?? null;
            $titreQuiz = $firstDataRow[3] ?? null;
            $formationNom = trim($firstDataRow[4] ?? '');

            // Vérification des données obligatoires
            if (empty($formationNom)) {
                return back()->with('error', "Le nom de la formation est obligatoire dans la première ligne de données.");
            }

            // Recherche de la formation
            $formation = Formation::where('titre', $formationNom)->first();
            if (!$formation) {
                return back()->with('error', "La formation '$formationNom' n'existe pas dans la base.");
            }
            // verification de quiz existant
            $quiz = Quiz::where('formation_id', $formation->id)->where('titre', $titreQuiz)->first();
            if ($quiz) {
                return back()->with('error', "Un quiz avec le titre '$titreQuiz' existe deja pour cette formation.");
            }

            // Création du quiz
            $quiz = Quiz::create([
                'titre' => $titreQuiz,
                'niveau' => $niveau,
                'duree' => $duree,
                'nb_points_total' => $nbPoints ?? 0, // Valeur par défaut si null
                'formation_id' => $formation->id,
            ]);

            $importedQuestions = 0;
            $errors = [];

            // Import des questions (en commençant à l'index 1 pour sauter l'en-tête)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                $questionText = $row[6] ?? null; // Colonne G (index 6)
                if (empty($questionText)) {
                    continue;
                }

                $repA = $row[7] ?? ''; // Colonne H
                $repB = $row[8] ?? ''; // Colonne I
                $repC = $row[9] ?? ''; // Colonne J
                $bonnesLettres = strtoupper(trim($row[10] ?? '')); // Colonne K
                $type = $row[11] ?? 'QCM'; // Colonne L

                try {
                    DB::beginTransaction();

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

                    $bonnes = array_filter(array_map('trim', explode(',', $bonnesLettres)));
                    $correctIds = [];

                    foreach ($reponses as $lettre => $texte) {
                        if (empty($texte)) continue;

                        $isCorrect = in_array($lettre, $bonnes);
                        $reponse = Reponse::create([
                            'question_id' => $question->id,
                            'text' => $texte,
                            'is_correct' => $isCorrect,
                            'position' => 1,
                        ]);

                        if ($isCorrect) {
                            $correctIds[] = $reponse->id;
                        }
                    }

                    if (empty($correctIds)) {
                        throw new \Exception("Aucune réponse correcte définie pour la question");
                    }

                    $question->update([
                        'correct_reponses_ids' => json_encode($correctIds)
                    ]);

                    DB::commit();
                    $importedQuestions++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errors[] = "Ligne " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            if ($importedQuestions === 0) {
                return back()->with('error', empty($errors)
                    ? 'Aucune question valide trouvée dans le fichier.'
                    : 'Importation échouée: ' . implode(', ', $errors));
            }

            $message = "Importation réussie: $importedQuestions questions importées";
            if (!empty($errors)) {
                $message .= "<br>" . count($errors) . " erreurs";
            }

            return back()->with(
                empty($errors) ? 'success' : 'warning',
                new \Illuminate\Support\HtmlString($message)
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
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
            // Envoyer une notification pour le nouveau quiz
            if ($newQuiz->status === 'actif') {
                $this->notificationService->notifyQuizAvailable(
                    $newQuiz->titre,
                    $newQuiz->id
                );
            }

            return redirect()->route('quiz.edit', $newQuiz->id)
                ->with('success', 'Quiz dupliqué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }

    public function disable($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->update(['status' => 'inactif']);

            return redirect()->route('quiz.index')->with('success', 'Quiz désactivé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la désactivation du quiz : ' . $e->getMessage());
        }
    }

    public function enable($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->update(['status' => 'actif']);

            return redirect()->route('quiz.index')->with('success', 'Quiz réactivé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la réactivation du quiz : ' . $e->getMessage());
        }
    }

    public function downloadQuizModel()
    {
        $filePath = public_path('models/quiz/quiz.xlsx');

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Le fichier modèle est introuvable.');
        }

        $fileName = 'modele_import_quiz.xlsx';

        return Response::download($filePath, $fileName);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $quiz = Quiz::findOrFail($id);

            // Supprimer les réponses associées
            QuizParticipationAnswer::whereHas('participation', function ($query) use ($id) {
                $query->where('quiz_id', $id);
            })->delete();

            // Supprimer les participations
            QuizParticipation::where('quiz_id', $id)->delete();

            // Supprimer les questions et leurs réponses
            $questions = Questions::where('quiz_id', $id)->get();
            foreach ($questions as $question) {
                Reponse::where('question_id', $question->id)->delete();
                $question->delete();
            }

            // Supprimer le quiz
            $quiz->delete();

            return redirect()->route('quiz.index')
                ->with('success', 'Quiz supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('quiz.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du quiz : ' . $e->getMessage());
        }
    }
}
