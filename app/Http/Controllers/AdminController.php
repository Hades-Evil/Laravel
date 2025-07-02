<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\VendorRequest;
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
    $products = Product::OrderBy('created_at','DESC')->paginate(10);
    return view('admin.products',compact('products'));

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

public function vendor_requests()
{
    $vendor_requests = VendorRequest::orderBy('id','DESC')->paginate(10);
    return view('admin.vendor-requests',compact('vendor_requests'));
}

public function vendor_request_view($id)
{
    $vendor_request = VendorRequest::find($id);
    return view('admin.vendor-request-view',compact('vendor_request'));
}

public function vendor_request_approve($id)
{
    $vendor_request = VendorRequest::find($id);
    $vendor_request->status = 'approved';
    $vendor_request->save();

    // Send notification to the vendor
    // ...

    return redirect()->route('admin.vendor.requests')->with('status', 'Vendor request approved successfully!');
}

public function vendor_request_decline($id)
{
    $vendor_request = VendorRequest::find($id);
    $vendor_request->status = 'declined';
    $vendor_request->save();

    // Send notification to the vendor
    // ...

    return redirect()->route('admin.vendor.requests')->with('status', 'Vendor request declined successfully!');
}

public function vendorRequests()
    {
        $vendorRequests = VendorRequest::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.vendor-requests', compact('vendorRequests'));
    }

    public function approveVendorRequest(Request $request, $id)
    {
        $vendorRequest = VendorRequest::findOrFail($id);
        
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $vendorRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Update user type to admin (since vendor/shop owner should have admin role)
        $vendorRequest->user->update([
            'utype' => 'ADM'
        ]);

        return redirect()->route('admin.vendor.requests')->with('success', 'Vendor request approved successfully!');
    }

    public function rejectVendorRequest(Request $request, $id)
    {
        $vendorRequest = VendorRequest::findOrFail($id);
        
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $vendorRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.vendor.requests')->with('success', 'Vendor request rejected.');
    }

    public function product_view($id)
{
    $product = Product::with(['category', 'brand'])->findOrFail($id);
    return view('admin.product-view', compact('product'));
}
}
