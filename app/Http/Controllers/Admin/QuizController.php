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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quiz = $this->quizeService->getById($id);
        $formations = Formation::all();
        return view('admin.quizzes.edit', compact('quiz', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuizStoreRequest $request, string $id)
    {
        $this->quizeService->update($id, $request->validated());

        return redirect()->route('quiz.index')
            ->with('success', 'Le quiz a été mis à jour avec succès.');
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

            // 2. Création de la question liée au quiz
            $questionData = $request->input('question');
            $questionData['quiz_id'] = $quiz->id;
            $question = Questions::create($questionData);

            // 3. Création de la réponse liée à la question
            $reponseData = $request->input('reponse');
            $reponseData['question_id'] = $question->id;
            Reponse::create($reponseData);

            DB::commit();

            return redirect()->route('quiz.index')->with('success', 'Quiz, question et réponse créés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
