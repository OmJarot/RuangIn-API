<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            "id" => "admin",
            "name" => "admin",
            "password" => "admin",
            "jurusan_id" => "admin",
            "level" => "admin",
        ]);

        for ($i = 0; $i < 20; $i++) {
            User::query()->create([
                "id" => "$i",
                "name" => "user $i",
                "password" => Hash::make("test"),
                "level" => "user",
                "jurusan_id" => "tpl 2023",
            ]);
        }
    }
}
