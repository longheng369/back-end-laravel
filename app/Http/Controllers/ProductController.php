<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    // public function addFavorite(Request $request, $productId)
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $product = Products::find($productId);
    //     if (!$product) {
    //         return response()->json(['message' => 'Product not found'], 404);
    //     }

    //     // Check if the product is already a favorite for the user
    //     if (!$user->favorites()->where('product_id', $productId)->exists()) {
    //         $user->favorites()->attach($productId, ['is_favorite' => true]);
    //         return response()->json(['message' => 'Product added to favorites'], 200);
    //     }

    //     return response()->json(['message' => 'Product is already a favorite'], 200);
    // }

    public function addFavorite(Request $request, $productId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Products::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Check if the product is already a favorite for the user
        if (!$user->favorites()->where('product_id', $productId)->exists()) {
            $user->favorites()->attach($productId);
            return response()->json(['message' => 'Product added to favorites'], 200);
        }

        return response()->json(['message' => 'Product is already a favorite'], 200);
    }



    public function removeFavorite(Request $request, $productId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Products::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $user->favorites()->detach($productId);
        return response()->json(['message' => 'Product removed from favorites'], 200);
    }

    public function getFavoriteProducts()
    {
        $products = Products::with('favoritedBy')->get();
        return response()->json($products);
    }

    public function getUserFavorites($userId)
    {
        $user = User::with('favorites')->find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user->favorites);
    }


    public function showFavorites()
    {
        $userId = auth()->id();
        // Eager load the favorites with additional relationships
        $user = User::with([
            'favorites.votes',
            'favorites.comments'
        ])->find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Get the IDs of the products favorited by the user
        $favoriteProductIds = $user->favorites->pluck('id')->toArray();

        // Retrieve all products
        $products = Products::with('votes', 'comments')->get();

        // Map over the products to customize the output
        $favorites = $products->map(function ($product) use ($favoriteProductIds) {
            // Calculate upvotes and downvotes
            $upvotes = $product->votes->where('is_upvote', 1)->count();
            $downvotes = $product->votes->where('is_upvote', 0)->count();

            // Determine if the product is favorited by the user
            $isFavorite = in_array($product->id, $favoriteProductIds);

            // Add these counts and the relationships to the product data
            return [
                'id' => $product->id,
                'image' => $product->image,
                'header' => $product->header,
                'title' => $product->title,
                'price' => $product->price,
                'details' => $product->details,
                'category_type' => $product->category_id,
                'published_at' => $product->published_at,
                'active' => $product->active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'upvotes_counts' => $upvotes,
                'downvote_counts' => $downvotes,
                'is_favorite' => $isFavorite, // Set the is_favorite attribute
                'votes' => $product->votes,
                'comments' => $product->comments->map(function ($comment) {
                    return [
                        'body' => $comment->body,
                        'user_name' => $comment->user->name
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'Favorites retrieved successfully',
            'data' => $favorites
        ]);
    }


    // public function getItemsWithLikes(Request $request)
    // {
        
    //     $products = Products::all(); 
    //     $likedProducts = $request->user()->likedProduct()->pluck('product_id')->toArray();
    //     $favoriteItems = $request->user()->favorites()->pluck('product_id')->toArray();

    //     return response()->json([
    //         'data' => $products,
    //         'likedItems' => $likedProducts,
    //         'favoriteItems' => $favoriteItems
    //     ]);
    // }

    public function getItemsWithLikes(Request $request)
    {
        // Fetching all products associated with the current user
        $products = Products::with(['votes', 'comments.user'])
        ->where('active', true)
        ->orderBy('created_at', 'desc')
        ->withCount([
            'votes as upvotes_count' => function ($query) {
                $query->where('is_upvote', true);
            },
            'votes as downvotes_count' => function ($query) {
                $query->where('is_upvote', false);
            }
        ])->get()
        ->map(function ($product) use ($request) {
            return [
                'id' => $product->id,
                'image' => $product->image,
                'header' => $product->header,
                'title' => $product->title,
                'price' => $product->price,
                'details' => $product->details,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'published_at' => $product->published_at,
                'active' => $product->active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'upvotes_counts' => $product->upvotes_count,
                'downvote_counts' => $product->downvotes_count,
                'votes' => $product->votes, // Assuming you handle votes similarly
                'comments' => $product->comments->map(function ($comment) use ($request) {
                    // $user = $request->user();
                    // $isUserComment = $user ? $user->id === $comment->user_id : false;
                    return [
                        'body' => $comment->body,
                        'user_name' => $comment->user->name, // Assuming the user model has a name field
                        // Include any other relevant information
                    ];
                }),
            ];
        });

        // Fetching liked products of the user
        $likedProducts = $request->user()->likedProducts()->pluck('product_id')->toArray();

        // Fetching favorite products of the user
        $favoriteItems = $request->user()->favorites()->orderBy('products.id', 'desc')->pluck('product_id')->toArray();

        return response()->json([
            'data' => $products,
            'likedItems' => $likedProducts,
            'favoriteItems' => $favoriteItems
        ]);
    }


}
