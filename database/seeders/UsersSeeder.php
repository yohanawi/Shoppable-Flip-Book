<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $adminUser = User::create([
            'name'              => 'Administrator',
            'email'             => 'admin@demo.com',
            'password'          => Hash::make('demo'),
            'role'              => 'Administrator',
            'email_verified_at' => now(),
        ]);

        $customerUser = User::create([
            'name'              => 'Customer User',
            'email'             => 'customer@demo.com',
            'password'          => Hash::make('demo'),
            'role'              => 'Customer',
            'email_verified_at' => now(),
        ]);
    }
}
