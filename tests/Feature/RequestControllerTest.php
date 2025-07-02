<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Request;
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
    public function testCreateSuccess(): void {
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
                    "user" => $user->name,
                    "ruangan" => $ruangan->name,
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

    public function testCreateValidationError(): void {
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

    public function testCreateBentrok(): void {
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

    public function testCreateDateNotValid(): void {
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

    public function testGetMy(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();
        $this->actingAs($user)
            ->get("/api/request/$user->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => $user->name,
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ]);
    }

    public function testGetMyByStatus(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();
        $this->actingAs($user)
            ->get("/api/request/$user->id?status=accept")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => $user->name,
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ]);
    }

    public function testSearch(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $ruangan = Ruangan::first();
        $response = $this->actingAs($user)
            ->get("/api/request")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => "piter",
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);
    }

    public function testSearchByStatus(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $ruangan = Ruangan::first();
        $response = $this->actingAs($user)
            ->get("/api/request?status=accept")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => "piter",
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);
    }

    public function testSearchByDate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $ruangan = Ruangan::first();
        $response = $this->actingAs($user)
            ->get("/api/request?date=2025-07-03")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => "piter",
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);

    }

    public function testSearchByGedung(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $ruangan = Ruangan::first();
        $response = $this->actingAs($user)
            ->get("/api/request?gedung=Gedung A")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => "piter",
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);

    }

    public function testUpdateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();
        $request = Request::first();
        $request->status = "waiting";
        $request->save();

        $this->actingAs($user)
            ->put("/api/request/$request->id", [
                "ruangan_id" => $ruangan->id,
                "title" => "title update",
                "description" => "description update",
                "date" => "2025-07-19",
                "start" => "14:00",
                "end" => "17:00",
            ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "user" => $user->name,
                    "ruangan" => $ruangan->name,
                    "title" => "title update",
                    "description" => "description update",
                    "date" => "2025-07-19",
                    "start" => "14:00",
                    "end" => "17:00",
                    "status" => "waiting"
                ]
            ]);

        $request = $user->requests()->first();
        self::assertNotNull($request);
        self::assertEquals($user->id, $request->user_id);
        self::assertEquals($ruangan->id, $request->ruangan_id);
        self::assertEquals("title update", $request->title);
        self::assertEquals("description update", $request->description);
        self::assertEquals("2025-07-19", $request->date);
        self::assertEquals("14:00:00", $request->start);
        self::assertEquals("17:00:00", $request->end);
        self::assertEquals("waiting", $request->status);
    }

    public function testUpdateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $request = Request::first();
        $request->status = "waiting";
        $request->save();

        $this->actingAs($user)
            ->put("/api/request/$request->id", [
                "ruangan_id" => "",
                "title" => "",
                "description" => "",
                "date" => "",
                "start" => "",
                "end" => ""
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "ruangan_id" => [
                        "The ruangan id field is required."
                    ],
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

    public function testUpdateNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();

        $this->actingAs($user)
            ->put("/api/request/tidak ada", [
                "ruangan_id" => $ruangan->id,
                "title" => "title update",
                "description" => "description update",
                "date" => "2025-07-19",
                "start" => "14:00",
                "end" => "17:00",
            ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" =>[
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testUpdateNotFoundAccept(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();
        $request = Request::first();

        $this->actingAs($user)
            ->put("/api/request/$request->id", [
                "ruangan_id" => $ruangan->id,
                "title" => "title update",
                "description" => "description update",
                "date" => "2025-07-19",
                "start" => "14:00",
                "end" => "17:00",
            ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" =>[
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testUpdateRuanganNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();
        $request = Request::first();
        $request->status = "waiting";
        $request->save();

        $this->actingAs($user)
            ->put("/api/request/$request->id", [
                "ruangan_id" => "salah",
                "title" => "title update",
                "description" => "description update",
                "date" => "2025-07-19",
                "start" => "14:00",
                "end" => "17:00",
            ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" =>[
                        "Not found"
                    ]
                ]
            ]);

    }

    public function testUpdateBentrok(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();

        $request = Request::create([
            "user_id" => $user->id,
            "ruangan_id" => $ruangan->id,
            "title" => "title test",
            "description" => "description test",
            "date" => "2026-07-03",
            "start" => "16:00",
            "end" => "17:00",
        ]);

        $this->actingAs($user)
            ->put("/api/request/$request->id", [
                "ruangan_id" => $ruangan->id,
                "title" => "title update",
                "description" => "description update",
                "date" => "2025-07-03",
                "start" => "14:00",
                "end" => "18:00",
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Request bentrok"
                    ]
                ]
            ]);
    }

    public function testGetByRuangan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();

        $response = $this->actingAs($user)
            ->get("/api/gedung/" . $ruangan->gedung->id . "/ruangan/" . $ruangan->id . "/request")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => $user->name,
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);
    }

    public function testGetByRuanganByDate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $ruangan = Ruangan::first();

        $response = $this->actingAs($user)
            ->get("/api/gedung/" . $ruangan->gedung->id . "/ruangan/" . $ruangan->id . "/request?date=2025-07-03")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user" => $user->name,
                        "ruangan" => $ruangan->name,
                        "title" => "title test",
                        "description" => "description test",
                        "date" => "2025-07-03",
                        "start" => "16:00:00",
                        "end" => "17:00:00",
                        "status" => "accept"
                    ]
                ]
            ])->json();

        self::assertEquals(1, count($response["data"]));
        self::assertEquals(1, $response["meta"]["total"]);
    }

    public function testDelete(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");
        $request = Request::first();

        $this->actingAs($user)
            ->delete("/api/request/$request->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("admin");

        $this->actingAs($user)
            ->delete("/api/request/notfound")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testDeleteForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class, RequestSeeder::class]);

        $user = User::find("2023");
        $request = Request::first();

        $this->actingAs($user)
            ->delete("/api/request/$request->id")
            ->assertStatus(403);
    }


}
