<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuizStoreRequest;
use App\Mail\NewQuizNotification;
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

        // Envoyer une notification FCM aux stagiaires de la formation
        $formation = $quiz->formation;
        if ($formation && method_exists($formation, 'stagiaires')) {
            $stagiaires = $formation->stagiaires()->with('user')->get();
            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user && $stagiaire->user->fcm_token) {
                    $title = 'Nouveau quiz disponible';
                    $body = "Un nouveau quiz \"{$quiz->titre}\" est disponible dans votre formation.";
                    $data = [
                        'quiz_id' => (string)$quiz->id,
                        'formation_id' => (string)$quiz->formation_id,
                        'type' => 'quiz',
                        'event' => 'created',
                    ];
                    $this->notificationService->sendFcmToUser($stagiaire->user, $title, $body, $data);
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                }
            }
        }
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



    /**
     * Mise à jour d'un quiz existant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'quiz' => 'required|array',
            'quiz.titre' => 'required|string',
            'quiz.description' => 'nullable|string',
            'quiz.niveau' => 'nullable|string',
            'quiz.duree' => 'nullable|integer',
            'quiz.formation_id' => 'required|exists:formations,id',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.reponses' => 'required|array|min:1',
            'questions.*.reponses.*.id' => 'nullable|exists:reponses,id',
            'questions.*.reponses.*.text' => 'required|string',
        ], [
            'quiz_id.required' => 'L\'ID du quiz est obligatoire.',
            'quiz_id.exists' => 'Le quiz spécifié n\'existe pas.',
            'quiz.titre.required' => 'Le titre du quiz est obligatoire.',
            'quiz.formation_id.required' => 'La formation associée au quiz est obligatoire.',
            'questions.*.text.required' => 'Le texte de la question est obligatoire.',
            'questions.*.type.required' => 'Le type de question est obligatoire.',
            'questions.*.points.required' => 'Le nombre de points pour la question est obligatoire.',
            'questions.*.reponses.required' => 'Les réponses à la question sont obligatoires.',
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
                // Suppression des questions marquées pour suppression
                if (!empty($questionInput['id']) && !empty($questionInput['_delete'])) {
                    $question = $quiz->questions()->find($questionInput['id']);
                    if ($question) {
                        $question->correspondancePairs()->delete();
                        $question->reponses()->delete();
                        if ($question->media_url && file_exists(public_path($question->media_url))) {
                            @unlink(public_path($question->media_url));
                        }
                        $question->delete();
                    }
                    continue;
                }

                // Mise à jour ou création de la question
                $question = !empty($questionInput['id'])
                    ? $quiz->questions()->find($questionInput['id'])
                    : $quiz->questions()->create($questionInput);

                if (!$question) continue;

                if (!empty($questionInput['id'])) {
                    if (!empty($questionInput['media_url']) && $question->media_url !== $questionInput['media_url']) {
                        if ($question->media_url && file_exists(public_path($question->media_url))) {
                            @unlink(public_path($question->media_url));
                        }
                    }
                    $question->update($questionInput);
                }

                $reponsesInput = $questionInput['reponses'] ?? [];
                $reponseIds = [];
                $leftItems = [];
                $rightItems = [];

                // Supprimer les anciennes paires de correspondance si nécessaire
                if ($question->type === 'correspondance') {
                    $question->correspondancePairs()->delete();
                }

                // Gestion des réponses
                foreach ($reponsesInput as $reponseInput) {
                    $reponseData = [
                        'text' => $reponseInput['text'],
                        'is_correct' => $question->type === 'correspondance' ? null : ($reponseInput['is_correct'] ?? 0),
                        'position' => $reponseInput['position'] ?? null,
                        'match_pair' => $reponseInput['match_pair'] ?? null,
                        'bank_group' => $reponseInput['bank_group'] ?? null,
                        'flashcard_back' => $reponseInput['flashcard_back'] ?? null,
                    ];

                    $reponse = !empty($reponseInput['id'])
                        ? $question->reponses()->find($reponseInput['id'])
                        : $question->reponses()->create($reponseData);

                    if ($reponse && !empty($reponseInput['id'])) {
                        $reponse->update($reponseData);
                    }

                    $reponseIds[] = $reponse->id;

                    // Préparation des items pour correspondance
                    if ($question->type === 'correspondance') {
                        $itemData = [
                            'id' => $reponse->id,
                            'text' => $reponse->text,
                            'bank_group' => $reponse->bank_group,
                        ];

                        if ($reponse->match_pair === 'left') {
                            $leftItems[] = $itemData;
                        } elseif ($reponse->match_pair === 'right') {
                            $rightItems[] = $itemData;
                        }
                    }
                }

                // Suppression des réponses non incluses
                $question->reponses()->whereNotIn('id', $reponseIds)->delete();

                // Création des paires de correspondance
                if ($question->type === 'correspondance') {
                    foreach ($leftItems as $leftItem) {
                        $rightItem = collect($rightItems)->firstWhere('bank_group', $leftItem['bank_group']);

                        if ($rightItem) {
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

            // Envoyer une notification FCM aux stagiaires de la formation
            $formation = $quiz->formation;
            if ($formation && method_exists($formation, 'stagiaires')) {
                $stagiaires = $formation->stagiaires()->with('user')->get();
                foreach ($stagiaires as $stagiaire) {
                    if ($stagiaire->user && $stagiaire->user->fcm_token) {
                        $title = 'Quiz mis à jour';
                        $body = "Le quiz \"{$quiz->titre}\" a été mis à jour dans votre formation.";
                        $data = [
                            'quiz_id' => (string)$quiz->id,
                            'formation_id' => (string)$quiz->formation_id,
                            'type' => 'quiz',
                            'event' => 'updated',
                        ];
                        $this->notificationService->sendFcmToUser($stagiaire->user, $title, $body, $data);
                        \App\Models\Notification::create([
                            'user_id' => $stagiaire->user->id,
                            'type' => $data['type'],
                            'title' => $title,
                            'message' => $body,
                            'data' => $data,
                            'read' => false,
                        ]);
                    }
                }
            }
            return redirect()->route('quiz.index')->with('success', 'Quiz mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
    /**
     * Crée une nouvelle question dans un quiz existant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNewQuestion(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'text' => 'required|string',
            'question.type' => 'required|string',
            'points' => 'required|integer|min:1',
            'reponses' => 'required|array|min:1',
            'reponses.*.text' => 'required|string',

        ]);

        DB::beginTransaction();

        try {
            $quiz = Quiz::findOrFail($request->input('quiz_id'));

            $questionInput = [
                'quiz_id' => $quiz->id,
                'text' => $request->input('text'),
                'type' => $request->input('question')['type'],
                'explication' => $request->input('explication') ?? null,
                'astuce' => $request->input('astuce') ?? null,
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
                    'is_correct' => $reponseInput['is_correct'] ?? ($question->type === 'correspondance' ? null : 0),
                    'position' => $reponseInput['position'] ?? null,
                    'match_pair' => $reponseInput['match_pair'] ?? null,
                    'bank_group' => $reponseInput['bank_group'] ?? null,
                    'flashcard_back' => $reponseInput['flashcard_back'] ?? null,
                ]);

                if ($question->type === 'correspondance') {
                    $itemData = [
                        'id' => $reponse->id,
                        'text' => $reponse->text,
                        'bank_group' => $reponse->bank_group,
                    ];

                    if ($reponse->match_pair === 'left') {
                        $leftItems[] = $itemData;
                    } elseif ($reponse->match_pair === 'right') {
                        $rightItems[] = $itemData;
                    }
                }
            }

            // Création des paires de correspondance
            if ($question->type === 'correspondance') {
                foreach ($leftItems as $leftItem) {
                    // Trouver l'élément right avec le même bank_group
                    $rightItem = collect($rightItems)->firstWhere('bank_group', $leftItem['bank_group']);

                    if ($rightItem) {
                        $existingPair = CorrespondancePair::where('question_id', $question->id)
                            ->where(function ($query) use ($leftItem, $rightItem) {
                                $query->where('left_id', $leftItem['id'])
                                    ->orWhere('right_id', $rightItem['id']);
                            })
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

            return redirect()->route('quiz.edit', $quiz)->with('success', 'Nouvelle question créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la question : ' . $e->getMessage());
        }
    }




    /**
     * Crée un nouveau quiz, une nouvelle question et les réponses associées.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'reponse.match_pair' => 'required|array', // Changé à required
            'reponse.bank_group' => 'required|array', // Changé à required
            'reponse.flashcard_back' => 'nullable|array',
        ], [
            'quiz.titre.required' => 'Le titre du quiz est obligatoire.',
            'question.type.required' => 'Le type de question est obligatoire.',
            'reponse.text.required' => 'Les réponses sont obligatoires.',
            'reponse.text.*.required' => 'Chaque réponse doit être renseignée.',
            'reponse.match_pair.required' => 'Le type de correspondance est obligatoire.',
            'reponse.bank_group.required' => 'Le groupe de banque est obligatoire.',
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

            // Première passe: créer toutes les réponses
            foreach ($reponses['text'] as $index => $text) {
                $reponse = Reponse::create([
                    'question_id' => $question->id,
                    'text' => $text,
                    'is_correct' => $reponses['is_correct'][$index] ?? null,
                    'position' => $reponses['position'][$index] ?? null,
                    'match_pair' => $reponses['match_pair'][$index],
                    'bank_group' => $reponses['bank_group'][$index],
                    'flashcard_back' => $reponses['flashcard_back'][$index] ?? null,
                ]);

                // Stocker les éléments pour la correspondance
                if ($questionData['type'] === 'correspondance') {
                    if ($reponses['match_pair'][$index] === 'left') {
                        $leftItems[] = [
                            'id' => $reponse->id, // Utiliser l'ID réel de la réponse
                            'text' => $text,
                            'bank_group' => $reponses['bank_group'][$index]
                        ];
                    } elseif ($reponses['match_pair'][$index] === 'right') {
                        $rightItems[] = [
                            'id' => $reponse->id, // Utiliser l'ID réel de la réponse
                            'text' => $text,
                            'bank_group' => $reponses['bank_group'][$index]
                        ];
                    }
                }
            }

            // Deuxième passe: créer les paires de correspondance
            if ($questionData['type'] === 'correspondance') {
                foreach ($leftItems as $leftItem) {
                    // Trouver l'élément right avec le même bank_group
                    $rightItem = collect($rightItems)->firstWhere('bank_group', $leftItem['bank_group']);

                    if ($rightItem) {
                        // Vérifier si la paire existe déjà
                        $existingPair = CorrespondancePair::where('question_id', $question->id)
                            ->where('left_id', $leftItem['id'])
                            ->orWhere('right_id', $rightItem['id'])
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

            // Envoyer une notification FCM uniquement aux stagiaires rattachés à la formation du quiz
            $formationId = $quiz->formation_id;
            $stagiaires = \App\Models\Stagiaire::where('formation_id', $formationId)->with('user')->get();
            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user && $stagiaire->user->fcm_token) {
                    $title = 'Nouveau quiz disponible';
                    $body = 'Un nouveau quiz "' . $quiz->titre . '" est disponible.';
                    $data = [
                        'quiz_id' => (string)$quiz->id,
                        'formation_id' => (string)$formationId,
                        'type' => 'quiz',
                        'event' => 'created',
                    ];
                    $this->notificationService->sendFcmToUser($stagiaire->user, $title, $body, $data);
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                }
            }
            return redirect()->route('quiz.index')->with('success', 'Quiz, question et réponses créés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du quiz : ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email de notification à tous les stagiaires
     * qui ont des catalogues de formation, pour leur signaler
     * qu'un nouveau quiz est disponible.
     *
     * @param \App\Models\Quiz $quiz
     * @return void
     */
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

    /**
     * Importe un fichier Excel (.xlsx ou .xls) contenant des questions pour un quiz.
     * Le fichier doit contenir les colonnes suivantes:
     *   - A: Niveau du quiz
     *   - B: Durée du quiz
     *   - C: Nombre de points total pour le quiz
     *   - D: Titre du quiz
     *   - E: Nom de la formation
     *   - F: Type de question (QCM, ouvert, etc.)
     *   - G: Texte de la question
     *   - H: Réponse A
     *   - I: Réponse B
     *   - J: Réponse C
     *   - K: Bonnes lettres (séparées par des virgules)
     *
     * Les colonnes peuvent être dans n'importe quel ordre, mais doivent être présentes.
     * Si une colonne est vide, la ligne est ignorée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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


    /**
     * Importe des questions et leurs réponses à partir d'un fichier Excel
     * Le fichier Excel doit avoir les colonnes suivantes:
     *  - B: Texte de la question
     *  - C, D, E: Textes des réponses
     *  - F: Lettres des réponses correctes (séparées par des virgules)
     * Les questions et réponses sont créées pour le quiz dont l'ID est fourni en paramètre
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

            // Envoyer une notification FCM aux stagiaires de la formation
            $formation = $newQuiz->formation;
            if ($formation && method_exists($formation, 'stagiaires')) {
                $stagiaires = $formation->stagiaires()->with('user')->get();
                foreach ($stagiaires as $stagiaire) {
                    if ($stagiaire->user && $stagiaire->user->fcm_token) {
                        $title = 'Quiz dupliqué';
                        $body = "Un quiz \"{$newQuiz->titre}\" a été dupliqué dans votre formation.";
                        $data = [
                            'quiz_id' => (string)$newQuiz->id,
                            'formation_id' => (string)$newQuiz->formation_id,
                            'type' => 'quiz',
                            'event' => 'duplicated',
                        ];
                        $this->notificationService->sendFcmToUser($stagiaire->user, $title, $body, $data);
                        \App\Models\Notification::create([
                            'user_id' => $stagiaire->user->id,
                            'type' => $data['type'],
                            'title' => $title,
                            'message' => $body,
                            'data' => $data,
                            'read' => false,
                        ]);
                    }
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

    /**
     * Désactive un quiz.
     *
     * @param int $id ID du quiz à désactiver
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
