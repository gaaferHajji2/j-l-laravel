<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $passwordEnc = Hash::make(fake()->password());
        for ($i = 0; $i < 1000; $i++) {
            $data[] =
            [
                'name' => fake()->firstName(),
                'email' => fake()->unique()->email(),
                'password' => $passwordEnc,
            ];
        }
        foreach (array_chunk($data, 100) as $chunk) {
            User::insert($chunk);
        }
    }
}
