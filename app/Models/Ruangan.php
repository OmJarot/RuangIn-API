<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use SoftDeletes;
    protected $table = "ruangans";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        "name",
        "gedung_id",
        "status"
    ];

    protected $attributes = [
        'status' => 'off',
    ];

    function gedung():BelongsTo {
        return $this->belongsTo(Gedung::class, "gedung_id", "id");
    }

    function requests(): HasMany {
        return $this->hasMany(Request::class, "ruangan_id", "id");
    }
}
