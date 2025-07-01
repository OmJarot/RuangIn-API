<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\User;
use Database\Seeders\GedungSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\RuanganSearchSeed;
use Database\Seeders\RuanganSeeder;
use Database\Seeders\UserSearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RuanganControllerTest extends TestCase
{
    public function testCreate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);
        $gedung = Gedung::first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/gedung/$gedung->id/ruangan", [
                "name" => "ruang 666"
            ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "ruang 666",
                    "gedung" => "Gedung A"
                ]
            ]);

        $ruangan = Ruangan::first();
        self::assertEquals("ruang 666", $ruangan->name);
    }

    public function testCreateNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/gedung/gaada/ruangan", [
                "name" => "ruang 666"
            ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);

    }

    public function testCreateForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/gedung/gaada/ruangan", [
                "name" => "ruang 666"
            ])->assertStatus(403);
    }

    public function testGetRuangan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan/$ruangan->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => $ruangan->id,
                    "name" => $ruangan->name,
                    "gedung" => $ruangan->gedung->name
                ]
            ]);
    }

    public function testGetRuanganNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan/tidak")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testGetGedungNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $ruangan = Ruangan::first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/gedung/tidak/ruangan/$ruangan->id")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/gedung/$gedung->id/ruangan/$ruangan->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => "true"
            ]);

        $ruangan = Ruangan::find($ruangan->id);
        self::assertNull($ruangan);
    }

    public function testDeleteRuanganNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/gedung/$gedung->id/ruangan/salah")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testDeleteGedungNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/gedung/salah/ruangan/salah")
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
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->delete("/api/gedung/$gedung->id/ruangan/$ruangan->id")
            ->assertStatus(403);
    }

    public function testSwitch(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->patch("/api/gedung/$gedung->id/ruangan/$ruangan->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
        $ruangan = Ruangan::first();
        self::assertEquals("on", $ruangan->status);
    }

    public function testSwitchRuanganNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->patch("/api/gedung/$gedung->id/ruangan/salah")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }

    public function testSwitchGedungNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->patch("/api/gedung/salah/ruangan/salah")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ]);
    }
    public function testSwitchForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSeeder::class]);

        $gedung = Gedung::first();
        $ruangan = Ruangan::first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->patch("/api/gedung/$gedung->id/ruangan/$ruangan->id")
            ->assertStatus(403);
    }

    public function testSearch(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSearchSeed::class]);

        $gedung = Gedung::first();
        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchByName(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSearchSeed::class]);

        $gedung = Gedung::first();
        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan?name=Ruang")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchByStatus(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSearchSeed::class]);

        $gedung = Gedung::first();
        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan?status=off")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchByPage(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSearchSeed::class]);

        $gedung = Gedung::first();
        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan?size=5&page=2")
            ->assertStatus(200)
            ->json();

        self::assertEquals(5, count($response["data"]));
        self::assertEquals(2, $response["meta"]["current_page"]);
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class, RuanganSearchSeed::class]);

        $gedung = Gedung::first();
        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/$gedung->id/ruangan?name=tidakada")
            ->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response["data"]));
        self::assertEquals(0, $response["meta"]["total"]);
    }


}
