<?php

namespace App\Http\Controllers;

use App\Events\NewCommentEvent;
use App\Models\Comments;
use App\Models\Products;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comments::all();

        return response()->json(['comments' => $comments]);
    }

    // public function store(Request $request, Products $product)
    // {
    //     $request->validate([
    //         'body'=>'required|string' 
    //     ]);

    //     $comment = Comments::create([
    //         'product_id' => $product->id,
    //         'user_id' => $request->user()->id,
    //         'body'=> $request->body
    //     ]);

    //     return response()->json($comment,201);
    // }

    public function store(Request $request, Products $product)
    {
        $request->validate([
            'body' => 'required|string' 
        ]);

        $comment = Comments::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'body' => $request->body
        ]);


        return response()->json($comment, 201);
    }

    public function show($id, Request $request) // Rename the function to show or something appropriate
{
    $product = Products::with(['votes', 'comments.user'])
        ->where('active', true)
        ->where('id', $id) // Filter to get only the specific product
        ->orderBy('created_at', 'desc')
        ->withCount([
            'votes as upvotes_count' => function ($query) {
                $query->where('is_upvote', true);
            },
            'votes as downvotes_count' => function ($query) {
                $query->where('is_upvote', false);
            },
        ])->first(); // Use first() instead of get() to return a single model instance

    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404); // Optionally handle the case where the product is not found
    }

    // Since we are retrieving a single product, we don't need to map over a collection
    $productData = [
        'id' => $product->id,
        'image' => $product->image,
        'header' => $product->header,
        'title' => $product->title,
        'price' => $product->price,
        'details' => $product->details,
        'color' => $product->color,
        'size' => $product->size,
        'scale' => $product->scale,
        'category_type' => $product->category_type,
        'published_at' => $product->published_at,
        'active' => $product->active,
        'created_at' => $product->created_at,
        'updated_at' => $product->updated_at,
        'upvotes_counts' => $product->upvotes_count,
        'downvote_counts' => $product->downvotes_count,
        'votes' => $product->votes, // Assuming you handle votes similarly
        'comments' => $product->comments->map(function ($comment) use ($request) {
            return [
                'body' => $comment->body,
                'user_name' => $comment->user->name, // Assuming the user model has a name field
                'comment_id' => $comment->id
            ];
        }),
    ];

    return response()->json(['data' => $productData]);
}

}
