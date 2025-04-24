<?php


namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\ParametreRepositoryInterface;
use Illuminate\Support\Collection;


class ParametreRepository implements ParametreRepositoryInterface
{
    public function all(): Collection
    {
        return User::all();
    }

    public function find(int $id): ?User
    {
        return User::with('stagiaire')->where('id',$id)->first();
    }

    public function create(array $data): User
    {
        $data['password'] = bcrypt($data['password']);
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);

        if ($user) {
            return $user->delete();
        }

        return false;
    }
}
