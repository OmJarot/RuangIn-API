<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGedungRequest;
use App\Http\Resources\GedungResource;
use App\Models\Gedung;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function create(CreateGedungRequest $request): GedungResource {
        $this->authorize("create", Gedung::class);

        $data = $request->validated();
        $gedung = Gedung::create($data);

        return new GedungResource($gedung);
    }
}
