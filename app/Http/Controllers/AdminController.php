<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
{
    return view('admin.index');
}
    public function brands()
{
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view("admin.brands",compact('brands'));
}

public function categories()
 {
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view("admin.categories",compact('categories'));
 }

 public function category_add()
{
    return view("admin.category-add");
}

public function category_store(Request $request)
{        
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug',
        'image' => 'mimes:png,jpg,jpeg|max:2048'
    ]);
    $category = new Category();
    $category->name = $request->name;
    $category->slug = Str::slug($request->name);
    
    if($request->hasFile('image')) {
        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailImage($image,$file_name);
        $category->image = $file_name;        
    }
    
    $category->save();
    return redirect()->route('admin.categories')->with('status','Record has been added successfully !');
}

public function GenerateCategoryThumbailImage($image, $file_name)
{
    $destinationPath = public_path('uploads/categories');
    
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }
    
    $image->move($destinationPath, $file_name);
}

public function category_edit($id)
{
    $category = Category::find($id);
    return view('admin.category-edit',compact('category'));
}

public function category_update(Request $request)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug,'.$request->id,
        'image' => 'mimes:png,jpg,jpeg|max:2048'
    ]);
    $category = Category::find($request->id);
    $category->name = $request->name;
    $category->slug = $request->slug;
    if($request->hasFile('image'))
    {            
        if (File::exists(public_path('uploads/categories').'/'.$category->image)) {
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailImage($image,$file_name);   
        $category->image = $file_name;
    }        
    $category->save();    
    return redirect()->route('admin.categories')->with('status','Record has been updated successfully !');
}

public function category_delete($id)
{
    $category = Category::find($id);

    if (File::exists(public_path('uploads/categories').'/'.$category->image)) 
    {
        File::delete(public_path('uploads/categories').'/'.$category->image);
    }
    $category->delete();
    return redirect()->route('admin.categories')->with('status','Record has been deleted successfully !');
}

public function products()
{
    $user = Auth::user();
    
    // Main admin can see all products, shop owners can only see their own
    if ($user->isMainAdmin()) {
        $products = Product::with('user')->orderBy('created_at','DESC')->paginate(10);
    } else {
        $products = Product::where('user_id', $user->id)->orderBy('created_at','DESC')->paginate(10);
    }
    
    return view('admin.products', compact('products'));
}

public function product_view($id)
{
    $product = Product::with(['category', 'brand', 'user'])->find($id);
    $user = Auth::user();
    
    // Check if user has permission to view this product
    if (!$user->isMainAdmin() && !$product->isOwnedBy($user)) {
        return redirect()->route('admin.products')->with('error', 'You do not have permission to view this product.');
    }
    
    return view('admin.product-view', compact('product'));
}

public function product_add()
{
    $categories = Category::Select('id','name')->orderBy('name')->get();
    $brands = Brand::Select('id','name')->orderBy('name')->get();
    return view("admin.products-add",compact('categories','brands'));
}

public function product_store(Request $request)
{
    $request->validate([
        'name'=>'required',
        'slug'=>'required|unique:products,slug',
        'category_id'=>'required',
        'brand_id'=>'nullable',            
        'short_description'=>'required',
        'description'=>'required',
        'regular_price'=>'required',
        'sale_price'=>'nullable',
        'SKU'=>'required',
        'stock_status'=>'required',
        'featured'=>'required',
        'quantity'=>'required',
        'image'=>'required|mimes:png,jpg,jpeg|max:2048'            
    ]);
    $product = new Product();
    $product->user_id = Auth::id(); // Associate product with current user
    $product->name = $request->name;
    $product->slug = Str::slug($request->name);
    $product->short_description = $request->short_description;
    $product->description = $request->description;
    $product->regular_price = $request->regular_price;
    $product->sale_price = $request->sale_price;
    $product->SKU = $request->SKU;
    $product->stock_status = $request->stock_status;
    $product->featured = $request->featured;
    $product->quantity = $request->quantity;
    $current_timestamp = Carbon::now()->timestamp;
    if($request->hasFile('image'))
    {        
        if (File::exists(public_path('uploads/products').'/'.$product->image)) {
            File::delete(public_path('uploads/products').'/'.$product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)) {
            File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
        }            
    
        $image = $request->file('image');
        $imageName = $current_timestamp.'.'.$image->extension();
        $this->GenerateThumbnailImage($image,$imageName);            
        $product->image = $imageName;
    }
    $gallery_arr = array();
    $gallery_images = "";
    $counter = 1;
    if($request->hasFile('images'))
    {
        $oldGImages = explode(",",$product->images);
        foreach($oldGImages as $gimage)
        {
            if (File::exists(public_path('uploads/products').'/'.trim($gimage))) {
                File::delete(public_path('uploads/products').'/'.trim($gimage));
            }
            if (File::exists(public_path('uploads/products/thumbails').'/'.trim($gimage))) {
                File::delete(public_path('uploads/products/thumbails').'/'.trim($gimage));
            }
        }
        $allowedfileExtension=['jpg','png','jpeg'];
        $files = $request->file('images');
        foreach($files as $file){                
            $gextension = $file->getClientOriginalExtension();                                
            $check=in_array($gextension,$allowedfileExtension);            
            if($check)
            {
                $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;   
                $this->GenerateThumbnailImage($file,$gfilename);                    
                array_push($gallery_arr,$gfilename);
                $counter = $counter + 1;
            }
        }
        $gallery_images = implode(',', $gallery_arr);
    }
    $product->images = $gallery_images;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id ?: null;
    $product->save();
    return redirect()->route('admin.products')->with('status','Record has been added successfully !');
}

public function GenerateThumbnailImage($image, $file_name)
{
    $destinationPath = public_path('uploads/products');
    $destinationPathThumbnails = public_path('uploads/products/thumbnails');
    
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }
    
    if (!file_exists($destinationPathThumbnails)) {
        mkdir($destinationPathThumbnails, 0755, true);
    }
    $image->move($destinationPath, $file_name);
    
    copy($destinationPath . '/' . $file_name, $destinationPathThumbnails . '/' . $file_name);
}

public function product_edit($id)
{
    $product = Product::find($id);
    $user = Auth::user();
    
    // Check if user has permission to edit this product
    if (!$user->isMainAdmin() && !$product->isOwnedBy($user)) {
        return redirect()->route('admin.products')->with('error', 'You do not have permission to edit this product.');
    }
    
    $categories = Category::Select('id','name')->orderBy('name')->get();
    $brands = Brand::Select('id','name')->orderBy('name')->get();
    return view('admin.product-edit',compact('product','categories','brands'));
}

public function product_update(Request $request)
{
    $request->validate([
        'name'=>'required',
        'slug'=>'required|unique:products,slug,'.$request->id,
        'category_id'=>'required',
        'brand_id'=>'nullable',            
        'short_description'=>'required',
        'description'=>'required',
        'regular_price'=>'required',
        'sale_price'=>'nullable',
        'SKU'=>'required',
        'stock_status'=>'required',
        'featured'=>'required',
        'quantity'=>'required',
        'image'=>'mimes:png,jpg,jpeg|max:2048'            
    ]);
    
    $product = Product::find($request->id);
    $user = Auth::user();
    
    // Check if user has permission to update this product
    if (!$user->isMainAdmin() && !$product->isOwnedBy($user)) {
        return redirect()->route('admin.products')->with('error', 'You do not have permission to update this product.');
    }
    $product->name = $request->name;
    $product->slug = $request->slug;
    $product->short_description = $request->short_description;
    $product->description = $request->description;
    $product->regular_price = $request->regular_price;
    $product->sale_price = $request->sale_price;
    $product->SKU = $request->SKU;
    $product->stock_status = $request->stock_status;
    $product->featured = $request->featured;
    $product->quantity = $request->quantity;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id ?: null;
    
    $current_timestamp = Carbon::now()->timestamp;
    
if ($request->hasFile('image')) {
    if (file_exists(public_path('uploads/products') . '/' . $product->image)) {
        File::delete(public_path('uploads/products') . '/' . $product->image);
    }
    if (file_exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
        File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
    }

    $image = $request->file('image');
    $imageName = $current_timestamp . '.' . $image->extension();
    $this->GenerateThumbnailImage($image, $imageName);

    $product->image = $imageName;
}
$gallery_arr = array();
$gallery_images = "";
$counter = 1;

if ($request->hasFile('images')) {
    foreach (explode(',', $product->images) as $ofile) {
        if (File::exists(public_path('uploads/products') . '/' .$ofile)) {
            File::delete(public_path('uploads/products') . '/' .$ofile);
        }

        if (File::exists(public_path('uploads/products/thumbnails') . '/' .$ofile)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
        }
    }
$allowedfileExtension = ['jpg', 'png', 'jpeg'];
$files = $request->file('images');

foreach ($files as $file) {
    $gextension = $file->getClientOriginalExtension();
    $gcheck = in_array($gextension, $allowedfileExtension);

    if ($gcheck) {
        $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
        $this->GenerateThumbnailImage($file, $gfileName);
        array_push($gallery_arr, $gfileName);
        $counter = $counter + 1;
    }
}

$gallery_images = implode(',', $gallery_arr);

$product->images = $gallery_images;
}
$product->save();

return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
}

public function product_delete($id)
{
    $product = Product::find($id);
    $user = Auth::user();
    
    // Check if user has permission to delete this product
    if (!$user->isMainAdmin() && !$product->isOwnedBy($user)) {
        return redirect()->route('admin.products')->with('error', 'You do not have permission to delete this product.');
    }        
    if(File::exists(public_path('uploads/products').'/'.$product->image)) 
    {
        File::delete(public_path('uploads/products').'/'.$product->image);
    }
    if (File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)) 
    {
        File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
    }

    foreach (explode(',', $product->images) as $ofile) {
        if (File::exists(public_path('uploads/products') . '/' .$ofile)) {
            File::delete(public_path('uploads/products') . '/' .$ofile);
        }

        if (File::exists(public_path('uploads/products/thumbnails') . '/' .$ofile)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
        }
    }
    $product->delete();
    return redirect()->route('admin.products')->with('status','Record has been deleted successfully !');
} 

}