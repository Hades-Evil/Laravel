<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'name', 'slug', 'short_description', 'description', 
        'regular_price', 'sale_price', 'cost_price', 'SKU', 'stock_status', 
        'featured', 'quantity', 'image', 'images', 'category_id', 'brand_id'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Check if the current authenticated user owns this product
     */
    public function isOwnedBy($user)
    {
        return $this->user_id === $user->id;
    }
}
