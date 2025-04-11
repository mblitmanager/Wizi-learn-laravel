<?php


namespace App\Repositories;


use App\Models\QuestionReponse;
use App\Repositories\Interfaces\QuestionReponseRepositoryInterface;

class QuestionReponseRepository implements QuestionReponseRepositoryInterface
{
    public function getAll()
    {
        return QuestionReponse::all();
    }

    public function getById($id)
    {
        return QuestionReponse::find($id);
    }

    public function store(array $data)
    {
        return QuestionReponse::create($data);
    }

    public function update($id, array $data)
    {
        $qustion = QuestionReponse::find($id);
        if ($qustion) {
            $qustion->update($data);
            return $qustion;
        }
        return null;
    }

    public function destroy($id)
    {
        $qustion = QuestionReponse::find($id);
        return $qustion ? $qustion->delete() : false;
    }
}
