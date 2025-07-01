<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderby('id', 'DESC')->paginate(12);
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
