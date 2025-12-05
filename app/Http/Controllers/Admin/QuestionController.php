<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Questions;
use App\Models\Reponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Questions::find($id);
        return view('admin.question.show', compact('question'));
    }

    public function edit(string $id)
    {
        $question = Questions::with('reponses')->findOrFail($id);
        return view('admin.question.edit', compact('question'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $question = Questions::findOrFail($id);

            $request->validate([
                'text' => 'required|string',
                'type' => 'required|string',
                'points' => 'required|integer',
                'media_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:56048',
                'reponses.*.text' => 'required|string',
                'reponses.*.is_correct' => 'nullable|boolean',
            ]);

            // Upload image si présente
            if ($request->hasFile('media_url')) {
                $image = $request->file('media_url');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/questions'), $imageName);
                $question->media_url = 'uploads/questions/' . $imageName;
            }

            $question->text = $request->text;
            $question->type = $request->type;
            $question->points = $request->points;
            $question->explication = $request->explication;
            $question->astuce = $request->astuce;
            $question->save();

            // Mise à jour des réponses
            foreach ($request->reponses as $reponseId => $reponseData) {
                $reponse = Reponse::find($reponseId);

                if ($reponse) {
                    $isCorrect = isset($reponseData['is_correct']) && $reponseData['is_correct'] == '1' ? true : false;

                    // Vérifie si les champs sont modifiés
                    if (
                        $reponse->text !== $reponseData['text'] ||
                        $reponse->is_correct != $isCorrect
                    ) {
                        $reponse->update([
                            'text' => $reponseData['text'],
                            'is_correct' => $isCorrect,
                        ]);
                    }
                }
            }
        } catch (\Exception $exception) {
            // Gérer l'erreur
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de la question : ' . $exception->getMessage());
        }
        return redirect()->route('question.show', $question->id)->with('success', 'Question mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
