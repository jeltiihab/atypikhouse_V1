<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Address extends Model
{
    use HasFactory,SoftDeletes,PowerJoins;

    /**
     * @var string[]
     */
    protected $fillable = [
        "property_id", "street", "street_number", "postal_code", "city", "country", "region"
    ];

    public function Property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }



}
