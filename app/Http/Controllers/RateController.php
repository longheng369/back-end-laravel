<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        $rate = Rate::all();

        return response()->json(['rates' => $rate]);
    }

    public function store(Request $request, Products $product)
    {
        $request->validate([
            'rate' => 'required|integer|min:1|max:5',
        ]);

        $rate = Rate::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $request->user()->id],
            ['rate' => $request->rate]
        );

        return response()->json($rate, 200);
    }
}
