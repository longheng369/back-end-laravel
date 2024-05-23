<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use App\Models\Products;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    // all data 
    // public function index(Request $request)
    // {
    //     $products = Products::with(['votes', 'comments.user', 'rate'])
    //         ->where('active', true)
    //         ->withCount([
    //             'votes as upvotes_count' => function ($query) {
    //                 $query->where('is_upvote', true);
    //             },
    //             'votes as downvotes_count' => function ($query) {
    //                 $query->where('is_upvote', false);
    //             }
    //         ])->get()
    //         ->map(function ($product) use ($request) {
    //             return [
    //                 'id' => $product->id,
    //                 'image' => $product->image,
    //                 'header' => $product->header,
    //                 'title' => $product->title,
    //                 'price' => $product->price,
    //                 'details' => $product->details,
    //                 'quantity' => $product->quantity,
    //                 'scale' => $product->scale,
    //                 'category_type' => $product->category_type,
    //                 'published_at' => $product->published_at,
    //                 'active' => $product->active,
    //                 'created_at' => $product->created_at,
    //                 'updated_at' => $product->updated_at,
    //                 'upvotes_counts' => $product->upvotes_count,
    //                 'downvote_counts' => $product->downvotes_count,

    //                 'votes' => $product->votes, // Assuming you handle votes similarly
    //                 'comments' => $product->comments->map(function ($comment) use ($request) {
    //                     // $user = $request->user();
    //                     // $isUserComment = $user ? $user->id === $comment->user_id : false;
    //                     return [
    //                         'body' => $comment->body,
    //                         'user_name' => $comment->user->name, // Assuming the user model has a name field
    //                         // Include any other relevant information
    //                     ];
    //                 }),
    //             ];
    //         });
    //     return response()->json(['data' => $products]);
    // }


    public function index(Request $request)
    {
        $products = Products::with(['votes', 'comments.user'])
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->withCount([
                'votes as upvotes_count' => function ($query) {
                    $query->where('is_upvote', true);
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
                    'quantity' => $product->quantity,
                    'scale' => $product->scale,
                    'category_type' => $product->category_id,
                    'sub_category_type' => $product->sub_category_id,
                    'published_at' => $product->published_at,
                    'active' => $product->active,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                    'upvotes_count' => $product->upvotes_count,
                    

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
        return response()->json(['data' => $products]);
    }



    // new arrival
    public function newArrival (Request $request)
{
    // Assuming 'new_arrival' is a boolean field that marks new arrival products
    // You might need to adjust the field name based on your actual database schema
    $products = Products::with(['votes', 'comments.user'])
        ->where('active', true)
        ->orderBy('created_at', 'desc')  // Sort by creation date descending
        ->limit(4)  // Limit to only 4 products
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
                'quantity' => $product->quantity,
                'scale' => $product->scale,
                'category_type' => $product->category_type,
                'published_at' => $product->published_at,
                'active' => $product->active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'upvotes_counts' => $product->upvotes_count,
                'downvote_counts' => $product->downvotes_count,

                'votes' => $product->votes->map(function ($vote) {
                    return [
                        'is_upvote' => $vote->is_upvote,
                        'user_id' => $vote->user_id
                    ];
                }),
                'comments' => $product->comments->map(function ($comment) {
                    return [
                        'body' => $comment->body,
                        'user_name' => $comment->user->name, // Assuming the user model has a name field
                        // Include any other relevant information
                    ];
                }),
            ];
        });
    return response()->json(['data' => $products]);
}


    public function getSubCategory($id)
    {
        $subCategory = SubCategory::where('category_id',$id)->get();

        return response()->json([
            'status' => 200,
            'sub_category' => $subCategory
        ]);
    }

    public function getIsLiked()
    {
        $userId = auth()->id();
        // Fetch products liked by the specified user
        $likedProducts = Products::with(['votes', 'comments.user', 'rate'])
        ->whereHas('votes', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('is_upvote', true);
        })
        ->where('active', true)
        ->withCount([
            'votes as upvotes_count' => function ($query) {
                $query->where('is_upvote', true);
            },
            'votes as downvotes_count' => function ($query) {
                $query->where('is_upvote', false);
            }
        ])->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                // 'image' => $product->image,
                // 'header' => $product->header,
                // 'title' => $product->title,
                // 'price' => $product->price,
                // 'details' => $product->details,
                // 'quantity' => $product->quantity,
                // 'scale' => $product->scale,
                // 'category_type' => $product->category_type,
                // 'published_at' => $product->published_at,
                // 'active' => $product->active,
                // 'created_at' => $product->created_at,
                // 'updated_at' => $product->updated_at,
                // 'upvotes_counts' => $product->upvotes_count,
                // 'downvote_counts' => $product->downvotes_count,
                // 'votes' => $product->votes,
                // 'comments' => $product->comments->map(function ($comment) {
                //     return [
                //         'body' => $comment->body,
                //         'user_name' => $comment->user->name,
                //     ];
                // }),

            ];
        });

        return response()->json(['data' => $likedProducts]);
    }
    
    public function getCategories ()
    {
        $categories = Category::where('active',true)
        // ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['status' => 200, "categories" => $categories]);
    }
    
} 
