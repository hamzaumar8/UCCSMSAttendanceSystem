<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'john doe',
            'email' => 'john@gmail.com',
            'password' => bcrypt('password'),
        ]);
        // $user->createToken('JohnDoe')->plainTextToken;
        User::factory()->count(5)->create();
    }
}