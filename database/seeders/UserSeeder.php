<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $dataUser = [
            [
                'id' => 1,
                'email' => 'systemadmin@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('systemadmin007'),
                'role_id' => 1,
                'telegram_id' => 9383737,
                'remember_token' => null,
                'is_active' => 1
            ],
        ];

        User::unguard(); // Allow manual 'id' insertion
        foreach ($dataUser as $data) {
            User::create($data); // This is all you need
        }
        User::reguard();
    }
}
