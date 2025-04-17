<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuizStoreRequest;
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
        DB::beginTransaction();

        try {
            $quiz = Quiz::findOrFail($id);

            // Mise à jour du quiz
            $quiz->update($request->input('quiz'));

            // Récupérer les questions liées
            $questions = $quiz->questions;
            $questionData = $request->input('questions', []);

            // Gestion fichier pour chaque question (si besoin)
            if ($request->hasFile('question_media_file')) {
                $file = $request->file('question_media_file');

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                $extension = $file->getClientOriginalExtension();

                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->with('error', 'Le type de fichier "' . $extension . '" n’est pas autorisé.');
                }

                foreach ($questions as $index => $question) {
                    // Supprimer l’ancien fichier s’il existe
                    if ($question->media_url && Storage::disk('public')->exists($question->media_url)) {
                        Storage::disk('public')->delete($question->media_url);
                    }

                    // Enregistrer le nouveau
                    $path = $file->store('medias', 'public');

                    // ⚠️ Assure que l'index existe avant d'y ajouter un champ
                    if (isset($questionData[$index])) {
                        $questionData[$index]['media_url'] = $path;
                    }
                }
            }

            foreach ($questions as $i => $question) {
                // 🔒 Sécurité : vérifier que les données pour cette question existent
                if (!isset($questionData[$i])) {
                    Log::warning("Données manquantes pour la question index $i");
                    continue;
                }

                // Mise à jour de la question
                $question->update($questionData[$i]);

                $reponses = $questionData[$i]['reponses'] ?? [];

                // Supprimer les anciennes réponses supprimées du formulaire
                $submittedIds = collect($reponses)->pluck('id')->filter()->toArray();
                $question->reponses()->whereNotIn('id', $submittedIds)->delete();

                foreach ($reponses as $reponseData) {
                    if (isset($reponseData['id'])) {
                        $reponse = $question->reponses()->find($reponseData['id']);
                        if ($reponse) {
                            $reponse->update([
                                'text' => $reponseData['text'] ?? null,
                                'is_correct' => $reponseData['is_correct'] ?? null,
                                'position' => $reponseData['position'] ?? null,
                                'match_pair' => $reponseData['match_pair'] ?? null,
                                'bank_group' => $reponseData['bank_group'] ?? null,
                                'flashcard_back' => $reponseData['flashcard_back'] ?? null,
                            ]);
                        }
                    } else {
                        $question->reponses()->create([
                            'text' => $reponseData['text'] ?? null,
                            'is_correct' => $reponseData['is_correct'] ?? null,
                            'position' => $reponseData['position'] ?? null,
                            'match_pair' => $reponseData['match_pair'] ?? null,
                            'bank_group' => $reponseData['bank_group'] ?? null,
                            'flashcard_back' => $reponseData['flashcard_back'] ?? null,
                        ]);
                    }
                }

                // Mise à jour de la bonne réponse automatiquement
                $reponseCorrecte = $question->reponses()->where('is_correct', true)->first();
                if ($reponseCorrecte) {
                    $question->update([
                        'reponse_correct' => $reponseCorrecte->text
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('quiz.index')->with('success', 'Quiz, questions et réponses mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeAll(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. Création du quiz
            $quiz = Quiz::create($request->input('quiz'));

            // 2. Préparer les données de la question
            $questionData = $request->input('question');
            $questionData['quiz_id'] = $quiz->id;

            // 3. Gérer l'upload du fichier media_url (image, PDF, Word, etc.)
            if ($request->hasFile('question.media_url')) {
                $file = $request->file('question.media_url');

                // Extensions autorisées
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                $extension = $file->getClientOriginalExtension();

                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->with('error', 'Le type de fichier "' . $extension . '" n’est pas autorisé.');
                }

                // Stocker le fichier dans storage/app/public/medias
                $path = $file->store('medias', 'public');
                $questionData['media_url'] = $path; // On stocke juste le chemin dans la BDD
            }
            // 4. Création de la question
            $question = Questions::create($questionData);

            // 5. Création des réponses
            $reponses = $request->input('reponse');

            foreach ($reponses['text'] as $index => $text) {
                Reponse::create([
                    'question_id' => $question->id,
                    'text' => $text,
                    'is_correct' => $reponses['is_correct'][$index] ?? null,
                    'position' => $reponses['position'][$index] ?? null,
                    'match_pair' => $reponses['match_pair'][$index] ?? null,
                    'bank_group' => $reponses['bank_group'][$index] ?? null,
                    'flashcard_back' => $reponses['flashcard_back'][$index] ?? null,
                ]);
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
}
