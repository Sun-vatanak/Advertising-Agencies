<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $dataProfile = [
            [
                'user_id' => 1,
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone' => '1234567890',
                'address' => 'ST 2004, Sensokh, Phnom Penh',
                'gender_id' => 1,
                'photo' => 'users/no_photo.jpg',
            ],
        ];

        foreach ($dataProfile as $data) {
            $profile = new UserProfile();
            $profile->user_id = $data['user_id'];
            $profile->first_name = $data['first_name'];
            $profile->last_name = $data['last_name'];
            $profile->phone = $data['phone'];
            $profile->address = $data['address'];
            $profile->gender_id = $data['gender_id'];
            $profile->photo = $data['photo'];
            $profile->save();
        }
    }
}
