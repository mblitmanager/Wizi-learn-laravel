<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions
        $permissions = [
            'view stagiaires',
            'view active stagiaires',
            'view inactive stagiaires',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer le rôle formateur
        $formateurRole = Role::create(['name' => 'formateur']);

        // Assigner les permissions au rôle formateur
        $formateurRole->givePermissionTo([
            'view stagiaires',
            'view active stagiaires',
            'view inactive stagiaires',
        ]);
    }
}