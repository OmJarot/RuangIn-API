<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGedungRequest;
use App\Http\Resources\GedungCollection;
use App\Http\Resources\GedungResource;
use App\Models\Gedung;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function create(CreateGedungRequest $request): GedungResource {
        $this->authorize("create", Gedung::class);

        $data = $request->validated();
        $gedung = Gedung::create($data);

        return new GedungResource($gedung);
    }

    public function delete(string $id): JsonResponse {
        $gedung = Gedung::find($id);
        if (!$gedung){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Gedung $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("delete", $gedung);
        $gedung->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function getAll(): GedungCollection {
        $this->authorize("viewAny", Gedung::class);
        $gedung = Gedung::query()->get();
        return new GedungCollection($gedung);
    }

    public function getAllOn(): GedungCollection {
        $this->authorize("create", Gedung::class);
        $gedung = Gedung::query()->where("status", "=", "on")->get();
        return new GedungCollection($gedung);
    }

    public function switchStatus(string $id): JsonResponse {
        $gedung = Gedung::find($id);
        if (!$gedung){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Gedung $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("create", Gedung::class);
        $gedung->status = ($gedung->status == "on") ? "off" : "on";
        $gedung->save();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function update(string $id,CreateGedungRequest $request): GedungResource {
        $gedung = Gedung::find($id);
        if (!$gedung){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Gedung $id not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("update", $gedung);
        $data = $request->validated();
        $gedung->fill($data);
        $gedung->save();
        return new GedungResource($gedung);
    }
}
