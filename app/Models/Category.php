<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    Protected $fillable = [
        "title",
        "product_count",
        "total_sale"
    ];
}
