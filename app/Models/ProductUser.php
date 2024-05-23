<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUser extends Model
{
    use HasFactory;

    protected $table = 'product_user';

    // Define the relationship to the product
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Define the relationship to the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
