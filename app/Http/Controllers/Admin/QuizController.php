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
        $quiz = $this->quizeService->list();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quiz = $this->quizeService->show($id);
        $formations = Formation::all();
        $question = $quiz->questions->first();
        return view('admin.quizzes.edit', compact('quiz', 'formations', 'question'));
    }

    public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $quiz = Quiz::findOrFail($id);

        // Mise à jour du quiz
        $quiz->update($request->input('quiz'));

        // Récupérer la question liée
        $question = $quiz->questions()->first();
        $questionData = $request->input('question');

        // Gestion du fichier (image / doc / pdf / xlsx ...)
        if ($request->hasFile('question_media_file')) {
            $file = $request->file('question_media_file');

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
            $extension = $file->getClientOriginalExtension();

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->with('error', 'Le type de fichier "' . $extension . '" n’est pas autorisé.');
            }

            // Supprimer l’ancien fichier s’il existe
            if ($question->media_url && Storage::disk('public')->exists($question->media_url)) {
                Storage::disk('public')->delete($question->media_url);
            }

            // Enregistrer le nouveau
            $path = $file->store('medias', 'public');
            $questionData['media_url'] = $path;
        }

        // Mise à jour de la question
        $question->update($questionData);

        $reponses = $request->input('reponse');

        // Supprimer les anciennes réponses supprimées du formulaire
        $submittedIds = isset($reponses['id']) ? array_values($reponses['id']) : [];
        $question->reponses()->whereNotIn('id', $submittedIds)->delete();

        // Mise à jour des réponses existantes
        if (isset($reponses['id']) && is_array($reponses['id'])) {
            foreach ($reponses['id'] as $i => $reponseId) {
                $reponse = $question->reponses()->find($reponseId);
                if ($reponse) {
                    $reponse->update([
                        'text' => $reponses['text'][$i] ?? null,
                        'is_correct' => $reponses['is_correct'][$i] ?? null,
                        'position' => $reponses['position'][$i] ?? null,
                        'match_pair' => $reponses['match_pair'][$i] ?? null,
                        'bank_group' => $reponses['bank_group'][$i] ?? null,
                        'flashcard_back' => $reponses['flashcard_back'][$i] ?? null,
                    ]);
                }
            }
        }

        // Ajouter les nouvelles réponses (celles sans ID)
        foreach ($reponses['text'] as $i => $text) {
            if (!isset($reponses['id'][$i])) {
                $question->reponses()->create([
                    'text' => $text,
                    'is_correct' => $reponses['is_correct'][$i] ?? null,
                    'position' => $reponses['position'][$i] ?? null,
                    'match_pair' => $reponses['match_pair'][$i] ?? null,
                    'bank_group' => $reponses['bank_group'][$i] ?? null,
                    'flashcard_back' => $reponses['flashcard_back'][$i] ?? null,
                ]);
            }
        }

        DB::commit();

        return redirect()->route('quiz.index')->with('success', 'Quiz, question et réponses mis à jour avec succès.');
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
}
