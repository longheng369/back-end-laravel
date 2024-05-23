<?php

namespace App\Models;

// use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['image', 'title', 'active'];


     // Define the relationship to represent the subcategories
     public function subCategories()
     {
         return $this->hasMany(SubCategory::class);
     }

}
