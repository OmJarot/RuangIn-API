<?php

namespace App\Http\Controllers;

use App\Http\Requests\CraeteJurusanRequest;
use App\Http\Resources\JurusanCollection;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function create(CraeteJurusanRequest $request):JurusanResource {
        $this->authorize("create", User::class);

        $data = $request->validated();

        $jurusan = Jurusan::query()->make($data);
        $jurusan->id = $data["name"]." ".$data["angkatan"];

        if (Jurusan::query()->where("id", "=", $jurusan->id)->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "id" => [
                        "jurusan already registered"
                    ]
                ]
            ],400));
        }

        $jurusan->save();

        return new JurusanResource($jurusan);
    }

    public function get(string $id): JurusanResource {
        $jurusan = Jurusan::query()->where("id", "=", $id)->first();

        if (!$jurusan){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ],404));
        }
        $this->authorize("view", $jurusan);

        return new JurusanResource($jurusan);
    }

    public function delete(string $id): JsonResponse {
        $jurusan = Jurusan::query()->where("id", "=", $id)->first();

        if (!$jurusan){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ],404));
        }
        $this->authorize("delete", $jurusan);
        $jurusan->delete();

        return response()->json(["data" => true])->setStatusCode(200);
    }

    function search(Request $request): JurusanCollection {
        $this->authorize("viewAny", Jurusan::class);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $jurusans = Jurusan::query()->where(function (Builder $builder) use ($request) {
            $name = $request->query("name");
            if ($name) {
                $builder->where("name", "like", "%$name%");
            }
            $angkatan = $request->query("angkatan");
            if ($angkatan) {
                $builder->where("angkatan", "=", $angkatan);
            }
        });
        $jurusans = $jurusans->paginate(perPage: $size, page: $page);

        return new JurusanCollection($jurusans);
    }
}
