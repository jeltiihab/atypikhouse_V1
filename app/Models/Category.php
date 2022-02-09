<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $hidden = ['pivot'];

    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(Equipement::class, 'equipement_category');
    }


    public function properties(): HasMany
    {
        return $this->hasMany(Property::class)  ;
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }



}
