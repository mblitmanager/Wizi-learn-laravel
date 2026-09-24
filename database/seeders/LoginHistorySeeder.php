<?php

namespace Database\Seeders;

use App\Models\LoginHistories;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoginHistorySeeder extends Seeder
{
    public function run(): void
    {
        // [ville, appareil, navigateur, plateforme]
        $connexions = [
            ['Lyon', 'Desktop', 'Chrome', 'Windows'],
            ['Nantes', 'Mobile', 'Chrome', 'Android'],
            ['Bordeaux', 'Desktop', 'Firefox', 'Windows'],
            ['Paris', 'Desktop', 'Safari', 'macOS'],
            ['Marseille', 'Mobile', 'Safari', 'iOS'],
            ['Toulouse', 'Desktop', 'Edge', 'Windows'],
            ['Rennes', 'Tablet', 'Chrome', 'Android'],
            ['Lille', 'Desktop', 'Chrome', 'Linux'],
            ['Strasbourg', 'Mobile', 'Chrome', 'Android'],
            ['Nice', 'Desktop', 'Firefox', 'macOS'],
        ];

        $users = User::where('email', 'like', 'stagiaire%@wizi-learn.com')->orderBy('id')->take(10)->get();

        foreach ($connexions as $i => [$ville, $device, $browser, $platform]) {
            $user = $users[$i] ?? null;
            if (! $user) {
                break;
            }

            $loginAt = now()->subDays($i)->setTime(8 + $i, 30);
            $ip = '192.168.1.'.(10 + $i);

            LoginHistories::updateOrCreate(
                ['user_id' => $user->id, 'login_at' => $loginAt],
                [
                    'ip_address' => $ip,
                    'country' => 'France',
                    'city' => $ville,
                    'device' => $device,
                    'browser' => $browser,
                    'platform' => $platform,
                    'logout_at' => $loginAt->copy()->addMinutes(25 + $i * 3),
                ]
            );

            $user->forceFill(['last_login_at' => $loginAt, 'last_login_ip' => $ip])->save();
        }
    }
}
