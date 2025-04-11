<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\CommercialInterface;
use App\Repositories\Interfaces\FormateurInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CommercialService
{
    protected $commercialInterface;

    public function __construct(CommercialInterface $commercialInterface)
    {
        $this->commercialInterface = $commercialInterface;
    }

    public function list()
    {
        return $this->commercialInterface->all();
    }

    public function show($id)
    {
        return $this->commercialInterface->find($id);
    }
    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'commercial',
        ]);

        // 2. Associer l'utilisateur
        $data['user_id'] = $user->id;

        // Créer le quiz
        return $this->commercialInterface->create($data);
    }

    public function update(int $id, array $data)
    {

        $formateur = $this->commercialInterface->find($id);

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
        return $this->commercialInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->commercialInterface->delete($id);
    }
}
