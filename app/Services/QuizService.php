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

    public function show($id)
    {
        return $this->quizRepository->find($id);
    }

    public function update($id, array $data)
    {
        return $this->quizRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->quizRepository->delete($id);
    }

    public function getQuizzesByStagiaire($stagiaireId, $withQuestions = true)
    {
        return $this->quizRepository->getQuizzesByStagiaire($stagiaireId, $withQuestions);
    }

    public function getCategories()
    {
        return \Illuminate\Support\Facades\Cache::remember('quiz_categories', 60 * 24, function () {
            return $this->quizRepository->getUniqueCategories();
        });
    }
    public function getQuestionsByQuizId($quizId)
    {
        return $this->quizRepository->getQuestionsByQuizId($quizId);
    }

    public function submitQuizAnswers($quizId, $stagiaireId, array $answers)
    {
        return $this->quizRepository->submitQuizAnswers($quizId, $stagiaireId, $answers);
    }

    public function getQuizzesWithUserParticipations($stagiaireId, $userId, $withQuestions = true)
    {
        return $this->quizRepository->getQuizzesWithUserParticipations($stagiaireId, $userId, $withQuestions);
    }
}
