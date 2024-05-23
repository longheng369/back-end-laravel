<?php

namespace App\Models;

use App\Models\User;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Votes extends Model
{
    use HasFactory;

    protected $table = 'votes';

    protected $fillable = ['product_id', 'user_id', 'is_upvote'];

    // Define the relationship to the product
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Define the relationship to the user who made the vote
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
