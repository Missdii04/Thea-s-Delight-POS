<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ProductCategory; // ✅ Keep this

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
    
    // ✅ Keep ONLY this casting (remove any other category casting)
    protected $casts = [
        'category' => ProductCategory::class,
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];
    
    // ✅ Keep scopes (they're fine)
    public function scopeCakes($query)
    {
        return $query->whereIn('category', ProductCategory::getCakeCategories());
    }
    
    public function scopeAccessories($query)
    {
        return $query->whereIn('category', ProductCategory::getAccessoryCategories());
    }
    
    
    public function isCake(): bool
    {
        return in_array($this->category, ProductCategory::getCakeCategories());
    }
    
    public function isAccessory(): bool
    {
        return in_array($this->category, ProductCategory::getAccessoryCategories());
    }
}