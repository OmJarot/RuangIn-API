<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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

        User::query()->create([
            "id" => "2023",
            "name" => "piter",
            "password" => "piter",
            "jurusan_id" => "tpl 2023",
            "level" => "user",
        ]);
    }
}
