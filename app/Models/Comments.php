<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comments extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = ['product_id', 'user_id', 'body'];

    // Define the relationship to the product
    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }

    // Define the relationship to the user who made the comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
