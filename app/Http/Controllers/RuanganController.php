<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRuanganRequest;
use App\Http\Resources\RuanganCollection;
use App\Http\Resources\RuanganResource;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function search(string $gedungId, Request $request): RuanganCollection {
        $this->authorize("viewAny", Gedung::class);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $gedung = $this->getGedung($gedungId);
        $query = $gedung->ruangan();

        if ($name = $request->query("name")) {
            $query->where("name", "like", "%$name%");
        }

        if ($status = $request->query("status")){
            $query->where("status", "=", $status);
        }

        $ruangan = $query->paginate(perPage: $size, page: $page);
        return new RuanganCollection($ruangan);
    }

    private function getRuangan(Gedung $gedung, string $ruanganId): Ruangan{
        $ruangan = $gedung->ruangan()->find($ruanganId);
        if (!$ruangan){
            $this->notFound();
        }
        return $ruangan;
    }

    private function getGedung(string $gedungId): Gedung {
        $gedung = Gedung::find($gedungId);
        if (!$gedung){
            $this->notFound();
        }
        return $gedung;
    }

    private function notFound() {
        throw new HttpResponseException(response()->json([
            "errors" => [
                "message" => [
                    "Not found"
                ]
            ]
        ])->setStatusCode(404));
    }

}
