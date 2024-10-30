<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@edoc.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
