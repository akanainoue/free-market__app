<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 明示的に3人のユーザーを作成
        $userA = User::firstOrCreate(
            ['email' => 'usera@example.com'],
            [
                'name' => 'ユーザーA', 
                'password' => bcrypt('password')
            ]
        );

        $userB = User::firstOrCreate(
            ['email' => 'userb@example.com'],
            [
                'name' => 'ユーザーB', 
                'password' => bcrypt('password')
            ]
        );

        $userC = User::firstOrCreate(
            ['email' => 'userc@example.com'],
            [
                'name' => 'ユーザーC', 
                'password' => bcrypt('password')
            ]
        );
    }
}
