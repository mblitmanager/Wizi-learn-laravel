<?php


namespace App\Repositories\Interfaces;


use App\Models\QuestionReponse;

interface QuestionReponseRepositoryInterface
{
    public function all();
    public function find($id): ? QuestionReponse;
    public function create(array $data): QuestionReponse;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}
