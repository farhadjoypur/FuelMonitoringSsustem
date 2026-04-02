<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'email' => 'admin@gmail.com',
                'phone' => '01711111111',
                'password' => Hash::make('123456789'),
                'role' => UserRole::ADMIN,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'dc@gmail.com',
                'phone' => '01711111112',
                'password' => Hash::make('123456789'),
                'role' => UserRole::DC,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'tag-officer@gmail.com',
                'phone' => '01711111113',
                'password' => Hash::make('123456789'),
                'role' => UserRole::TAG_OFFICER,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
