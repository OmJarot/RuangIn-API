<?php

namespace Database\Seeders;

use App\Models\Request;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangan = Ruangan::first();
        $user = User::first();
        Request::create([
            "user_id" => $user->id,
            "ruangan_id" => $ruangan->id,
            "title" => "title test",
            "description" => "description test",
            "date" => "2025-07-03",
            "start" => "16:00",
            "end" => "17:00",
            "status" => "accept"
        ]);
    }

}
