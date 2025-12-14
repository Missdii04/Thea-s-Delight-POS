<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// You no longer need to import ProductCategory here if you only use its static methods.
// If you use the class name elsewhere (e.g., in method return types or parameters), keep the 'use' statement. 
// For simplicity, we assume you will use fully qualified names or rely on auto-loading for helper methods.


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'category',
        'stock_quantity',
        'image_path',
        'status',
    ];
    
    // 🛑 CRITICAL CHANGE: The 'category' cast has been removed.
    // This column will now be treated as a plain string from the database.
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];
    
    // The scopes and helper methods remain functional because they use 
    // the static helper methods on the Enum (e.g., ProductCategory::getCakeCategories()),
    // which return arrays of strings for comparison.
    
    public function scopeCakes($query)
    {
        // NOTE: Ensure your ProductCategory Enum file still exists and has its namespace correct.
        return $query->whereIn('category', \App\Enums\ProductCategory::getCakeCategories());
    }
    
    public function scopeAccessories($query)
    {
        return $query->whereIn('category', \App\Enums\ProductCategory::getAccessoryCategories());
    }
    
    
    public function isCake(): bool
    {
        // $this->category is now a string, which is correct for in_array()
        return in_array($this->category, \App\Enums\ProductCategory::getCakeCategories());
    }
    
    public function isAccessory(): bool
    {
        // $this->category is now a string
        return in_array($this->category, \App\Enums\ProductCategory::getAccessoryCategories());
    }
}
