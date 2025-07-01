<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRuanganRequest;
use App\Http\Resources\RuanganResource;
use App\Models\Gedung;
use App\Models\Ruangan;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function create(string $id, CreateRuanganRequest $request): RuanganResource {
        $this->authorize("create", Ruangan::class);

        $gedung = Gedung::query()->where("id", "=", $id)->first();
        if (!$gedung){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $data = $request->validated();

        $ruangan = $gedung->ruangan()->updateOrCreate($data);
        return new RuanganResource($ruangan);
    }

    public function get(string $gedungId, string $ruanganId): RuanganResource {
        $gedung = $this->getGedung($gedungId);
        $ruangan = $this->getRuangan($gedung, $ruanganId);

        return new RuanganResource($ruangan);
    }

    public function delete(string $gedungId, string $ruanganId): JsonResponse {
        $gedung = $this->getGedung($gedungId);
        $ruangan = $this->getRuangan($gedung, $ruanganId);

        $this->authorize("delete", $ruangan);
        $ruangan->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function switchStatus(string $gedungId, string $ruanganId): JsonResponse {
        $gedung = $this->getGedung($gedungId);
        $ruangan = $this->getRuangan($gedung, $ruanganId);

        $this->authorize("update", $ruangan);
        $ruangan->status = ($gedung->status == "on") ? "off" : "on";
        $ruangan->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    private function getRuangan(Gedung $gedung, string $ruanganId){
        $ruangan = $gedung->ruangan()->find($ruanganId);
        if (!$ruangan){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $ruangan;
    }

    private function getGedung(string $gedungId) {
        $gedung = Gedung::find($gedungId);
        if (!$gedung){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $gedung;
    }

}
