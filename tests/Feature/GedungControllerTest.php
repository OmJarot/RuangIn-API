<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\User;
use Database\Seeders\GedungSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GedungControllerTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/gedung", ["name" => "Gedung A"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "Gedung A",
                    "status" => "off"
                ]
            ]);
    }

    public function testCreateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/gedung", ["name" => ""])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testCreateForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/gedung", ["name" => "Gedung A"])
            ->assertStatus(403);
    }

    public function testDeleteSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $gedung = Gedung::query()->first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/gedung/".$gedung->id)
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $collection = Gedung::query()->get();
        self::assertTrue($collection->isEmpty());
    }

    public function testDeleteNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/gedung/tidak ada")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Gedung tidak ada not found"
                    ]
                ]
            ]);
    }

    public function testForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $gedung = Gedung::query()->first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->delete("/api/gedung/".$gedung->id)
            ->assertStatus(403);

    }

    public function testGetAll(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung")
            ->assertStatus(200)
            ->json();

        self::assertCount(1, $response["data"]);
    }

    public function testGetAllForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/gedung")
            ->assertStatus(403);
    }

    public function testGetAllOn(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/gedung/on")
            ->assertStatus(200)
            ->json();

        self::assertCount(0, $response["data"]);
    }

    public function testSwitchStatus(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);

        $gedung = Gedung::query()->first();
        $user = User::find("admin");
        $this->actingAs($user)
            ->put("/api/gedung/status/$gedung->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
        $gedung = Gedung::query()->first();
        self::assertEquals("on", $gedung->status);
    }

    public function testUpdate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);
        $gedung = Gedung::query()->first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->put("/api/gedung/$gedung->id", ["name" => "Gedung B"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "Gedung B"
                ]
            ]);
        $gedung = Gedung::query()->first();
        self::assertEquals("Gedung B", $gedung->name);
    }

    public function testUpdateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);
        $gedung = Gedung::query()->first();

        $user = User::find("admin");
        $this->actingAs($user)
            ->put("/api/gedung/$gedung->id")
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, GedungSeeder::class]);
        $gedung = Gedung::query()->first();

        $user = User::find("2023");
        $this->actingAs($user)
            ->put("/api/gedung/$gedung->id")
            ->assertStatus(403);
    }


}
