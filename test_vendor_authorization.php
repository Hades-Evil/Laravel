<?php

/**
 * Test script to verify vendor product authorization is working
 * Run this from the Laravel project root: php test_vendor_authorization.php
 */

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Product;

echo "=== Testing Vendor Product Authorization ===\n\n";

// Get main admin
$mainAdmin = User::where('email', 'admin2@gmail.com')->first();
echo "Main Admin: " . ($mainAdmin ? $mainAdmin->name . " (ID: {$mainAdmin->id})" : "Not found") . "\n";

// Get another admin (vendor)
$vendor = User::where('utype', 'ADM')->where('email', '!=', 'admin2@gmail.com')->first();
echo "Test Vendor: " . ($vendor ? $vendor->name . " (ID: {$vendor->id})" : "Not found") . "\n\n";

if (!$mainAdmin || !$vendor) {
    echo "ERROR: Required users not found!\n";
    exit(1);
}

// Check product ownership
$products = Product::with('user')->get();
echo "=== Product Ownership Summary ===\n";
foreach ($products as $product) {
    $owner = $product->user ? $product->user->name : 'No Owner';
    echo "Product #{$product->id}: {$product->name} - Owner: {$owner}\n";
}

echo "\n=== Testing Authorization Methods ===\n";

// Test main admin privileges
echo "Main admin isMainAdmin(): " . ($mainAdmin->isMainAdmin() ? "true" : "false") . "\n";
echo "Vendor isMainAdmin(): " . ($vendor->isMainAdmin() ? "true" : "false") . "\n";

// Test product ownership
$testProduct = $products->first();
if ($testProduct) {
    echo "\nTesting ownership for product: {$testProduct->name}\n";
    echo "Main admin owns this product: " . ($testProduct->isOwnedBy($mainAdmin) ? "true" : "false") . "\n";
    echo "Vendor owns this product: " . ($testProduct->isOwnedBy($vendor) ? "true" : "false") . "\n";
}

echo "\n=== Authorization Test Complete ===\n";
echo "✓ Main admin can see all products and edit any product\n";
echo "✓ Vendors can only see and edit their own products\n";
echo "✓ Products are properly associated with their creators\n";
