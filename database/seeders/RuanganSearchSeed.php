<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\Ruangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuanganSearchSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gedung = Gedung::first();
        for ($i = 0; $i < 10; $i++) {
            Ruangan::create([
                "name" => "Ruang $i",
                "gedung_id" => $gedung->id
            ]);
        }
    }
}
