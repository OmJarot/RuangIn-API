<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Database\Seeders\JurusanSearchSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JurusanTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/jurusans", [
            "name" => "tpll",
            "angkatan" => 2023
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => "tpll 2023",
                    "name" => "tpll",
                    "angkatan" => "2023"
                ]
            ]);

        $jurusan = Jurusan::query()->where("id", "=", "tpll 2023")->first();
        self::assertNotNull($jurusan);
        self::assertEquals("tpll", $jurusan->name);
    }

    public function testCreateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/jurusans", [
            "name" => "",
            "angkatan" => 4023
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."],
                    "angkatan" => ["The angkatan field must be less than 2100."]
                ]
            ]);
    }

    public function testCreateAlreadyExist(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->post("/api/jurusans", [
            "name" => "tpl",
            "angkatan" => 2023
        ], [
            "API-Key" => "dba"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "jurusan already registered"
                    ]
                ]
            ]);
    }

    public function testCreateForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/jurusans", [
            "name" => "tpl",
            "angkatan" => 2023
        ])->assertStatus(403);
    }


    public function testGetJurusan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->get("/api/jurusans/tpl 2023")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "tpl 2023",
                    "name" => "tpl",
                    "angkatan" => "2023"
                ]
            ]);

        $jurusan = Jurusan::query()->where("id", "=", "tpl 2023")->first();
        self::assertNotNull($jurusan);
        self::assertEquals("tpl", $jurusan->name);
    }

    public function testGetJurusanNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->get("/api/jurusans/tpl 2024")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testGetForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/jurusans/tpl 2023")
            ->assertStatus(403);
    }

    public function testDeleteSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        Jurusan::create([
            "id" => "tpl 2024",
            "name" => "tpl",
            "angkatan" => 2023
        ]);
        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/jurusans/tpl 2024")
            ->assertStatus(200)
            ->assertJson([
                "data" => "ok"
            ]);

        $jurusan = Jurusan::query()->where("id", "=", "tpl 2024")->first();
        self::assertNull($jurusan);
    }

    public function testDeleteNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("admin");
        $this->actingAs($user)
            ->delete("/api/jurusans/tpl 2024")
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);

    }

    public function testForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->delete("/api/jurusans/tpl 2023")
            ->assertStatus(403);
    }

    public function testSearch(): void {
        $this->seed([JurusanSearchSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/jurusans")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(21, $response["meta"]["total"]);
    }

    public function testSearchByName(): void {
        $this->seed([JurusanSearchSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/jurusans?name=tpl")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }
    public function testSearchByAngkatan(): void {
        $this->seed([JurusanSearchSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)->get("/api/jurusans?angkatan=2023")
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(21, $response["meta"]["total"]);
    }

    public function testSearchNotFound(): void {
        $this->seed([JurusanSearchSeeder::class]);

        $user = User::find("admin");
        $response = $this->actingAs($user)
            ->get("/api/jurusans?name=tidak")
            ->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response["data"]));
        self::assertEquals(0, $response["meta"]["total"]);
    }

    public function testSearchForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->get("/api/jurusans")
            ->assertStatus(403);
    }
}
