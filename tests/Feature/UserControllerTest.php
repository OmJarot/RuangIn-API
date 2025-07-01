<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);
        $user = User::query()->find("admin");
        self::assertNotNull($user);

        $this->actingAs($user)
            ->post("/api/users",[
            "id" => "123",
            "name" => "piter",
            "password" => "piter",
            "jurusan_id" => "tpl 2023",
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "piter",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);
    }

    public function testCreateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);
        $user = User::query()->find("admin");
        self::assertNotNull($user);

        $this->actingAs($user)
            ->post("/api/users",[
                "id" => "",
                "name" => "",
                "password" => "",
                "jurusan_id" => "",
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "jurusan_id" => [
                        "The jurusan id field is required."
                    ]
                ]
            ]);
    }

    public function testCreateAlreadyRegisted(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);
        $user = User::query()->find("admin");
        self::assertNotNull($user);

        $this->actingAs($user)
            ->post("/api/users",[
                "id" => "admin",
                "name" => "admin",
                "password" => "admin",
                "jurusan_id" => "admin",
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Nim already registed"
                    ]
                ]
            ]);
    }

    public function testCreateWithOutLogin(): void {
        $this->post("/api/users",[
                "id" => "123",
                "name" => "piter",
                "password" => "piter",
                "jurusan_id" => "tpl 2023",
            ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function testLoginSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/login", [
            "id" => "2023",
            "password" => "piter"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "2023",
                    "name" => "piter",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);
    }

    public function testLoginValidationError() {
        $this->post("/api/users/login", [
            "id" => "",
            "password" => ""
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
    }

    public function testLoginWrong() {
        $this->post("/api/users/login", [
            "id" => "salah",
            "password" => "salah"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Nim or Password wrong"
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/users/update-password",[
                "oldPassword" => "piter",
                "newPassword" => "update",
                "retypePassword" => "update"
            ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::find("2023");
        self::assertTrue(Hash::check("update", $user->password));
    }

    public function testUpdatePasswordValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/users/update-password",[
                "oldPassword" => "",
                "newPassword" => "salah",
                "retypePassword" => "update"
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "oldPassword" => [
                        "The old password field is required."
                    ],
                    "retypePassword" => [
                        "The retype password field must match new password."
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordOldPasswordWrong(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->post("/api/users/update-password",[
                "oldPassword" => "salah",
                "newPassword" => "update",
                "retypePassword" => "update"
            ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Old password is wrong"
                    ]
                ]
            ]);

        $user = User::find("2023");
        self::assertTrue(Hash::check("piter", $user->password));
    }

    public function testLogoutSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::find("2023");
        $this->actingAs($user)
            ->delete("/api/users/logout")
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Logout success.'
            ]);

    }


}
