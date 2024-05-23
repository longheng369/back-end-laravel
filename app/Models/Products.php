<?php

namespace App\Models;

use App\Models\Rate;
use App\Models\User;
use App\Models\Votes;
use App\Models\Comments;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Products extends Model
{
    // use HasFactory;

    // protected $table = 'products';



    // protected $fillable = ['image', 'header', 'title', 'price', 'details', 'category_id','sub_category_id', 'published_at', 'active'];

    // // Define the relationship to the category
    // public function category()
    // {
    //     return $this->belongsTo(Category::class, 'category_id');
    // }

    // public function sub_category()
    // {
    //     return $this->belongsTo(SubCategory::class, 'sub_category_id');
    // }

    // // Define the relationship to votes
    // public function votes()
    // {
    //     return $this->hasMany(Votes::class, 'product_id');
    // }

    // // Define the relationship to comments
    // public function comments()
    // {
    //     return $this->hasMany(Comments::class, 'product_id');
    // }

    // // Define the relationship to rates
    // public function rates()
    // {
    //     return $this->hasMany(Rate::class);
    // }

    // // Define the relationship to users who favorited the product
    // public function favoritedBy()
    // {
    //     return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id');
    // }

    // public function upvotes()
    // {
    //     return $this->hasMany(Votes::class)->where('is_upvote',true);
    // }

    // public function downvotes()
    // {
    //     return $this->hasMany(Votes::class)->where('is_upvote',false);
    // }
    

    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['image', 'header', 'title', 'price', 'details','scale','size','color','category_id', 'sub_category_id', 'published_at', 'active'];

    /**
     * Define a many-to-one relationship with Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define a many-to-one relationship with SubCategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Define a one-to-many relationship with Votes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Votes::class,'product_id');
    }

    /**
     * Define a one-to-many relationship with Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class,'product_id');
    }

    /**
     * Define a one-to-many relationship with Rates.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Define a many-to-many relationship with User for favoritedBy.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id');
    }

    /**
     * Define a one-to-many relationship with Votes for upvotes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function upvotes(): HasMany
    {
        return $this->hasMany(Votes::class)->where('is_upvote', true);
    }

    /**
     * Define a one-to-many relationship with Votes for downvotes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downvotes(): HasMany
    {
        return $this->hasMany(Votes::class)->where('is_upvote', false);
    }

}
