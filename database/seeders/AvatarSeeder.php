<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Avatar;

class AvatarSeeder extends Seeder
{
    public function run()
    {
        Avatar::create([
            'name' => 'Classique',
            'image' => 'avatars/avatar1.png',
            'unlock_condition' => null,
            'price_points' => 0,
        ]);
        Avatar::create([
            'name' => 'Ninja',
            'image' => 'avatars/avatar2.png',
            'unlock_condition' => 'Débloquer 5 badges',
            'price_points' => 100,
        ]);
        Avatar::create([
            'name' => 'Scientifique',
            'image' => 'avatars/avatar3.png',
            'unlock_condition' => 'Atteindre le rang 3',
            'price_points' => 200,
        ]);
        Avatar::create([
            'name' => 'Champion',
            'image' => 'avatars/avatar4.png',
            'unlock_condition' => 'Cumuler 500 points',
            'price_points' => 300,
        ]);
    }
} 