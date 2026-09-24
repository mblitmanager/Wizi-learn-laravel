<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'stagiaires' => ['view', 'create', 'edit', 'delete'],
            'formateurs' => ['view', 'create', 'edit', 'delete'],
            'commerciaux' => ['view', 'create', 'edit', 'delete'],
            'pole relation client' => ['view', 'create', 'edit', 'delete'],
            'formations' => ['view', 'create', 'edit', 'delete'],
            'catalogue formations' => ['view', 'create', 'edit', 'delete'],
            'quiz' => ['view', 'create', 'edit', 'delete'],
            'medias' => ['view', 'create', 'edit', 'delete'],
            'partenaires' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ];

        $permissionNames = [];
        foreach ($permissions as $group => $actions) {
            foreach ($actions as $action) {
                $name = $action . ' ' . $group;
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'description' => ucfirst($action) . ' les ' . $group,
                        'group' => $group,
                        'is_active' => true,
                    ]
                );
                $permissionNames[$group . '.' . $action] = $name;
            }
        }

        Permission::whereIn('name', [
            'view active stagiaires',
            'view inactive stagiaires',
        ])->whereDoesntHave('roles')->delete();

        $roles = [
            'administrateur' => array_keys($permissionNames),
            'formateur' => [
                'stagiaires.view',
                'formations.view',
                'catalogue formations.view',
                'quiz.view',
                'medias.view',
            ],
            'commercial' => [
                'stagiaires.view',
                'stagiaires.create',
                'stagiaires.edit',
                'stagiaires.delete',
            ],
            'pole_relation_client' => [
                'stagiaires.view',
                'stagiaires.create',
                'stagiaires.edit',
                'stagiaires.delete',
                'pole relation client.view',
                'pole relation client.create',
                'pole relation client.edit',
                'pole relation client.delete',
            ],
            'stagiaire' => [
                'formations.view',
                'catalogue formations.view',
                'quiz.view',
                'medias.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissionKeys) {
            $role = Role::updateOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                [
                    'description' => 'Rôle ' . str_replace('_', ' ', $roleName),
                    'is_active' => true,
                    'is_protected' => $roleName === 'administrateur',
                ]
            );

            $role->syncPermissions(array_map(
                fn (string $key): string => $permissionNames[$key],
                $rolePermissionKeys
            ));
        }

        $roleMap = [
            'admin' => 'administrateur',
            'administrateur' => 'administrateur',
            'formatrice' => 'formateur',
            'commerciale' => 'commercial',
        ];

        User::query()->each(function (User $user) use ($roleMap, $roles): void {
            $roleName = $roleMap[$user->role] ?? $user->role;

            if (array_key_exists($roleName, $roles)) {
                $user->syncRoles([$roleName]);
            }
        });
    }
}
