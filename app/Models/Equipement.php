<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipement extends Model
{
    use HasFactory,SoftDeletes;

    protected $hidden = ['pivot'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'equipement_category');
    }
}
