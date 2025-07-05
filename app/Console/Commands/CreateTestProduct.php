<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Console\Command;

class CreateTestProduct extends Command
{
    protected $signature = 'test:create-product {email}';
    protected $description = 'Create a test product for a vendor';

    public function handle()
    {
        $email = $this->argument('email');
        $vendor = User::where('email', $email)->first();
        
        if (!$vendor) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $category = Category::first();
        if (!$category) {
            $this->error("No categories found!");
            return 1;
        }
        
        $product = new Product();
        $product->user_id = $vendor->id;
        $product->name = "Test Product for {$vendor->name}";
        $product->slug = "test-product-" . str_replace('@', '-', $vendor->email);
        $product->short_description = "Test product for vendor {$vendor->name}";
        $product->description = "This is a test product created by vendor {$vendor->name}";
        $product->regular_price = 99.99;
        $product->SKU = strtoupper(substr($vendor->name, 0, 2)) . '001';
        $product->stock_status = 'instock';
        $product->featured = false;
        $product->quantity = 10;
        $product->category_id = $category->id;
        $product->save();
        
        $this->info("Created test product '{$product->name}' for vendor {$vendor->name}");
        return 0;
    }
}
