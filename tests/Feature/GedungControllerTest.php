<?php

namespace Tests\Feature;

use App\Models\User;
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


}
