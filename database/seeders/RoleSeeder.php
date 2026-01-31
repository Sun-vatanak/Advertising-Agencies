<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'System Admin'],
            ['id' => 2, 'name' => 'Vendor'],
            ['id' => 3, 'name' => 'Delivery'],
            ['id' => 4, 'name' => 'Client'],
        ]);
    }
}
