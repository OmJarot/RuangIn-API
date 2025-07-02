<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\User;
use Database\Seeders\GedungSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\RequestSeeder;
use Database\Seeders\RuanganSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestControllerTest extends TestCase
{
    public function testRequestSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $user = User::find("2023");
        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan/$ruangan->id/request", [
                "title" => "title test",
                "description" => "description test",
                "date" => "2025-07-03",
                "start" => "16:00",
                "end" => "17:00"
            ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "user_id" => $user->id,
                    "ruangan_id" => $ruangan->id,
                    "title" => "title test",
                    "description" => "description test",
                    "date" => "2025-07-03",
                    "start" => "16:00",
                    "end" => "17:00",
                    "status" => "waiting"
                ]
            ]);

        $request = $user->requests()->first();
        self::assertNotNull($request);
        self::assertEquals($user->id, $request->user_id);
        self::assertEquals($ruangan->id, $request->ruangan_id);
        self::assertEquals("title test", $request->title);
        self::assertEquals("description test", $request->description);
        self::assertEquals("2025-07-03", $request->date);
        self::assertEquals("16:00:00", $request->start);
        self::assertEquals("17:00:00", $request->end);
        self::assertEquals("waiting", $request->status);
    }

    public function testRequestValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $user = User::find("2023");
        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan/$ruangan->id/request", [
                "title" => "",
                "description" => "",
                "date" => "",
                "start" => "",
                "end" => ""
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "title" => [
                        "The title field is required."
                    ],
                    "description" => [
                        "The description field is required."
                    ],
                    "date" => [
                        "The date field is required."
                    ],
                    "start" => [
                        "The start field is required."
                    ],
                    "end" => [
                        "The end field is required."
                    ]
                ]
            ]);
    }

    public function testRequestBentrok(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan/$ruangan->id/request", [
                "title" => "title test",
                "description" => "description test",
                "date" => "2025-07-03",
                "start" => "16:01",
                "end" => "16:49"
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Request bentrok"
                    ]
                ]
            ]);
    }

    public function testDateNotValid(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan/$ruangan->id/request", [
                "title" => "title test",
                "description" => "description test",
                "date" => "2025-07-01",
                "start" => "16:01",
                "end" => "16:49"
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Date not valid"
                    ]
                ]
            ]);
    }

    public function testForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan/$ruangan->id/request", [
                "title" => "title test",
                "description" => "description test",
                "date" => "2025-07-01",
                "start" => "16:01",
                "end" => "16:49"
            ])->assertStatus(403);

    }


}
