<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestRequest;
use App\Http\Requests\UpdateRequestRequest;
use App\Http\Resources\RequestCollection;
use App\Http\Resources\RequestResource;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use \App\Models\Request as Req;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    public function create(string $gedungId, string $ruangId, CreateRequestRequest $request): RequestResource {
        $this->authorize("create", Req::class);
        $gedung = $this->getGedung($gedungId);
        $ruangan = $this->getRuangan($gedung, $ruangId);

        $data = $request->validated();

        $req = $ruangan->requests()
            ->whereDate("date", $data["date"])
            ->where("status", "=", "accept")
            ->where(function ($q) use ($data) {
                $q->whereTime('start', '<', $data['end'])
                    ->whereTime('end', '>', $data['start']);
            })
            ->count();

        if ($req >= 1){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Request bentrok"
                    ]
                ]
            ])->setStatusCode(400));
        }

        if (Carbon::parse($data['date'])->lt(Carbon::today())){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Date not valid"
                    ]
                ]
            ])->setStatusCode(400));
        }

        $user = Auth::user();
        $request = Req::query()->make($data);
        $request->user_id = $user->id;
        $request->ruangan_id = $ruangId;
        $request->save();

        return new RequestResource($request);
    }

    public function update(string $id, UpdateRequestRequest $request): RequestResource {
        $db = Req::find($id);
        if (!$db || $db->status != "waiting"){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $this->authorize("update", $db);

        $data = $request->validated();

        $ruangan = Ruangan::query()->where("id", "=", $data["ruangan_id"])->first();
        if (!$ruangan){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $req = $ruangan->requests()->whereDate("date", $data["date"])
            ->where("id", "<>", $db->id)
            ->where("status", "=", "accept")
            ->where(function ($q) use ($data) {
                $q->whereTime('start', '<', $data['end'])
                    ->whereTime('end', '>', $data['start']);
            })->count();

        if ($req >= 1){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Request bentrok"
                    ]
                ]
            ])->setStatusCode(400));
        }

        if (Carbon::parse($data['date'])->lt(Carbon::today())){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Date not valid"
                    ]
                ]
            ])->setStatusCode(400));
        }

        $db->fill($data);
        $db->save();
        return new RequestResource($db);
    }

    public function getMy(string $id, Request $request): RequestCollection {
        $this->authorize("create", Req::class);

        $user = User::find($id);
        $status = $request->query("status");

        $query = $user->requests();
        if ($status){
            $query->where("status", "=", $status);
        }

        $request = $query->get();

        return new RequestCollection($request);
    }

    public function search(Request $request): RequestCollection {
        $this->authorize("viewAny", Req::class);

        $status = $request->query("status");
        $date = $request->query("date");

        $gedung = $request->query("gedung");
        $ruangan = $request->query("ruangan");
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $query = Req::query();

        if ($status) {
            $query->where("status", "=", $status);
        }
        if ($date) {
            try {
                $date = Carbon::parse($date)->format("Y-m-d");
            }catch (\Exception){
                $date = null;
            }
            if ($date != null){
                $query->whereDate("date", "=", $date);
            }
        }

        if ($ruangan){
            $query->whereHas("ruangan", function ($q) use ($ruangan){
                $q->where("name", "=" , $ruangan);
            });
        }
        if ($gedung) {
            $query->whereHas('ruangan.gedung', function ($q) use ($gedung) {
                $q->where('name', $gedung);
            });
        }
        $paginated = $query->paginate(perPage: $size, page: $page);

        return new RequestCollection($paginated);
    }

    public function getByRuangan(string $gedungId, string $ruangId, Request $request) {
        $this->authorize("viewAny", Req::class);
        $gedung = $this->getGedung($gedungId);
        $ruangan = $this->getRuangan($gedung, $ruangId);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $date = $request->query("date");

        $query = $ruangan->requests()->where("status", "=", "accept");

        if ($date) {
            try {
                $date = Carbon::parse($date)->format("Y-m-d");
            }catch (\Exception){
                $date = null;
            }
            if ($date != null){
                $query->whereDate("date", "=", $date);
            }
        }

        $paginated = $query->paginate(perPage: $size, page: $page);

        return new RequestCollection($paginated);
    }

    private function getRuangan(Gedung $gedung, string $ruanganId): Ruangan{
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

    private function getGedung(string $gedungId): Gedung {
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
