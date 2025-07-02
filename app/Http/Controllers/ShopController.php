<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        $products = $query->orderby('id', 'DESC')->paginate(12);
        $categories = Category::orderBy('name')->get();
        
        return view('shop', compact('products', 'categories'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', $product_slug)->get()->take(8);
        return view('details', compact('product', 'rproducts'));
    }
}
