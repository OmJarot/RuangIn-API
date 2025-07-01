<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::query()->create([
            "id" => "admin",
            "name" => "admin",
            "angkatan" => "2023"
        ]);

        Jurusan::query()->create([
            "id" => "tpl 2023",
            "name" => "tpl",
            "angkatan" => "2023"
        ]);
    }
}
