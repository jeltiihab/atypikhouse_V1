<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    use HasFactory;

    protected $casts = [
        "is_required"=>"boolean"
    ];

    public function Category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
