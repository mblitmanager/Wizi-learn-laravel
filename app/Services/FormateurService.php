<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\FormateurInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class FormateurService
{
    protected $formateurInterface;

    public function __construct(FormateurInterface $formateurInterface)
    {
        $this->formateurInterface = $formateurInterface;
    }

    public function list()
    {
        return $this->formateurInterface->all();
    }

    public function show($id)
    {
        return $this->formateurInterface->find($id);
    }
    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'formateur',
        ]);

        // 2. Associer l'utilisateur
        $data['user_id'] = $user->id;

        // Créer le quiz
        $formateur = $this->formateurInterface->create($data);
        $formateur->formations()->sync($data['formation_id']);

        return $formateur;
    }

    public function update(int $id, array $data)
    {
        $formateur = $this->formateurInterface->find($id);

        if (!$formateur) {
            throw new \Exception("Quiz not found");
        }
        $formateur->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $formateur->user->password,
        ]);

        unset($data['name'], $data['email'], $data['password']);

        // Mettre à jour le quiz
        return $this->formateurInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->formateurInterface->delete($id);
    }
}
