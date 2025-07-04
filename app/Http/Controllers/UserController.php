<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function create(CreateUserRequest $request): UserResource {
        $user = Auth::user();
        $this->authorize("create", User::class);

        $data = $request->validated();

        if (User::query()->where("id", "=", $data["id"])->count() >= 1){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Nim already registed"
                    ]
                ]
            ])->setStatusCode(400));
        }

        $user = User::query()->make($data);
        $user->password = Hash::make($data["password"]);
        $user->level = "user";
        $user->save();
        return new UserResource($user);
    }

    public function login(LoginUserRequest $request): UserResource {
        $data = $request->validated();

        $login = Auth::attempt([
            "id" => $data["id"],
            "password" => $data["password"]
        ]);

        if ($login){
            Session::regenerate();
            $user = Auth::user();
            return new UserResource($user);
        }else{
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Nim or Password wrong"
                    ]
                ]
            ])->setStatusCode(400));
        }
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse {
        $user = Auth::user();
        $this->authorize("update", $user);

        $data = $request->validated();
        if (Hash::check($data["oldPassword"], $user->password)){
            $user->password = Hash::make($data["newPassword"]);
            $user->save();
            return response()->json([
                "data" => true
            ])->setStatusCode(200);
        }else{
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Old password is wrong"
                    ]
                ]
            ])->setStatusCode(400));
        }
    }

    public function logout(Request $request): JsonResponse {
        Auth::logout();

        return response()->json([
            'message' => 'Logout success.'
        ]);
    }

    public function get(string $id): UserResource {
        $user = User::find($id);
        if (!$user){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "User $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("view", $user);
        return new UserResource($user);
    }

    public function delete(string $id): JsonResponse {
        $user = User::find($id);
        if (!$user){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "User $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("delete", $user);
        $user->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function update(string $id, UpdateUserRequest $request): UserResource {
        $this->authorize("create", User::class);
        $data = $request->validated();
        $user = User::find($id);
        if (!$user){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "User $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $user->fill($data);
        $user->password = Hash::make($data["password"]);
        $user->save();
        return new UserResource($user);
    }

    public function search(Request $request): UserCollection {
        $this->authorize("viewAny", User::class);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $query = User::query();

        if ($name = $request->query("name")) {
            $query->where("name", "like", "%$name%");
        }

        $jurusan = $request->query("jurusan");
        $angkatan = $request->query("angkatan");

        if ($jurusan || $angkatan) {
            $query->whereHas("jurusan", function ($bJurusan) use ($jurusan, $angkatan) {
                if ($jurusan) {
                    $bJurusan->where("jurusans.name", "=", $jurusan);
                }
                if ($angkatan) {
                    $bJurusan->where("jurusans.angkatan", "=", $angkatan);
                }
            });
        }

        $users = $query->paginate(perPage: $size, page: $page);

        return new UserCollection($users);
    }
}
