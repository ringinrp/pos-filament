<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    //1 category dapat memiliki banyak product

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
