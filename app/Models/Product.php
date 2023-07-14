<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    Protected $fillable = [
        "title",
        "category_id",
        "price",
        "description",
        "image",
    ];
}
