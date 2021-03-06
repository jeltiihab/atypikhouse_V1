<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;
use KirschbaumDevelopment\NovaComments\Commentable;

class Property extends Model
{



    use HasFactory,SoftDeletes,PowerJoins,Commentable;

    public $casts = [

        "dynamic_attributes"=>"json",
        "equipments" =>"json"
    ];

    protected $fillable = [
        "user_id", "category_id", "name", "rooms", "description", "surface", "hosting_capacity", "check_in_at", "check_out_at", "price", "is_activated", "images", "equipments", "dynamic_attributes"
    ];



    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
