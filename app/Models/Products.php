<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'price',
        'cost',
        'img_url',
        'description',
        'date',
        'estate',
        'Subcategories_id',
        'Promotions_id',
        'Brands_id',
        'Users_id'
    ];
}
