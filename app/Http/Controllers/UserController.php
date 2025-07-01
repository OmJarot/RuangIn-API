<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create(CreateUserRequest $request): UserResource {
        $user = Auth::user();
        $this->authorize("create", $user);

        $data = $request->validated();

        if (User::query()->where("id", "=", $data["id"])->count() > 1){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Nim already registed"
                    ]
                ]
            ])->setStatusCode(400));
        }

        $user = User::query()->make($data);
        $user->level = "user";
        $user->save();
        return new UserResource($user);
    }
}
