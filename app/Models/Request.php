<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;
    protected $table = "requests";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        "user_id",
        "ruangan_id",
        "title",
        "description",
        "date",
        "start",
        "end"
    ];

    protected $attributes = [
        'status' => 'waiting',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function ruangan(): BelongsTo {
        return $this->belongsTo(Ruangan::class, "ruangan_id", "id");
    }
}
