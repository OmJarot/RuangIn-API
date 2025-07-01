<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gedung extends Model
{
    use SoftDeletes;
    protected $table = "gedungs";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        "name",
        "status"
    ];

    protected $attributes = [
        'status' => 'off',
    ];

    public function ruangan(): HasMany {
        return $this->hasMany(Ruangan::class, "gedung_id", "id");
    }

}
