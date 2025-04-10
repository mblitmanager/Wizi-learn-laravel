<?php
namespace App\Services;


use App\Repositories\Interfaces\QuizRepositoryInterface;


class QuizService
{
    protected $quizRepositoryInterface;

    public function __construct(QuizRepositoryInterface $quizRepositoryInterface)
    {
        $this->quizRepositoryInterface = $quizRepositoryInterface;
    }

    public function list()
    {
        return $this->quizRepositoryInterface->all();
    }

    public function show($id)
    {
        return $this->quizRepositoryInterface->find($id);
    }
    public function create(array $data)
    {
        // Créer le quiz
        return $this->quizRepositoryInterface->create($data);
    }

    public function update(int $id, array $data)
    {
        $quiz = $this->quizRepositoryInterface->find($id);

        if (!$quiz) {
            throw new \Exception("Quiz not found");
        }

        // Mettre à jour le quiz
        return $this->quizRepositoryInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->quizRepositoryInterface->delete($id);
    }
}
