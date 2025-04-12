<?php
namespace App\Services;


use App\Repositories\Interfaces\QuizRepositoryInterface;


class QuizService
{
    protected $quizRepository;

    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function getAll()
    {
        return $this->quizRepository->all();
    }

    public function getById($id)
    {
        return $this->quizRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->quizRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->quizRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->quizRepository->delete($id);
    }

    public function getQuizzesByStagiaire($stagiaireId)
    {
        return $this->quizRepository->getQuizzesByStagiaire($stagiaireId);
    }
}
