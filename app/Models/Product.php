<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image_url',
        'description',
        'price',
        'quantity',
        'type',
    ];
}
