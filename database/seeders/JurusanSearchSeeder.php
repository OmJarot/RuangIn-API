<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JurusanSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::create([
            "id" => "admin",
            "name" => "admin",
            "angkatan" => 2023
        ]);
        for ($i = 0; $i < 20; $i++) {
            Jurusan::create([
                "id" => "tpl $i",
                "name" => "tpl $i",
                "angkatan" => 2023
            ]);

        }
        User::query()->create([
            "id" => "admin",
            "name" => "admin",
            "password" => Hash::make("admin"),
            "level" => "admin",
            "jurusan_id" => "admin",
        ]);
    }
}
