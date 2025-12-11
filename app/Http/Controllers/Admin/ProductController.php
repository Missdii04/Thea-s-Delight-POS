<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Str; // <-- Ensure this is imported
use App\Http\Requests\StoreProductRequest; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View; 
use Illuminate\Http\RedirectResponse; 
use Illuminate\Database\Query\Builder; // Added for type hinting on local query scope

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
{
    $query = Product::query()->orderBy('name');
    $search = $request->input('search');
    $filter = $request->input('filter', 'all'); // new
    $category = $request->input('category'); // new

    $query = Product::query();

    // Search filter (existing)
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('sku', 'like', "%$search%")
              ->orWhere('category', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }
   

   

    // NEW: Cake vs Add-on filter
    if ($filter === 'cakes') {
        $query->whereNotIn('category', [
             'Cake Topper', 'Candles', 'Greeting Card',
        ]);
    }

    if ($filter === 'addons') {
        $query->whereIn('category', [
             'Cake Topper', 
            'Candles', 'Greeting Card', ]);
    }

    // NEW: Sub-category filter
    if ($category) {
        $query->where('category', $category);
    }
     // 1. Fetch the list of all unique categories for the dropdown
    $allCategories = Product::select('category')->distinct()->pluck('category')->toArray();

    // NOW paginate the filtered results
    $products = $query->paginate(3)->withQueryString();

    return view('admin.products.index', compact('products','allCategories'));
}

public function adjustStock(Request $request, Product $product)
    {
        // 1. Validate the incoming data (ensures new_stock is a non-negative integer)
        $validated = $request->validate([
            'new_stock' => 'required|integer|min:0',
        ]);
        
        // 2. Update the product's stock quantity
        $product->update([
            'stock_quantity' => $validated['new_stock'],
        ]);
        
        // 3. Optional: Add stock adjustment history/log here if you have a logging system.

        // 4. Redirect back to the inventory or product list with a success message
        return redirect()
            ->back()
            ->with('success', "Stock for {$product->name} has been successfully updated to {$validated['new_stock']} units.");
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Pass an empty Product model instance and fetch all products for listing context
        $product = new Product();
        $products = Product::orderBy('name')->get();
        
        // This is the dedicated view for creation, as required by the standard resource route
        return view('admin.products.create', compact('product', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

       
        
        // Handle Image Upload
        if ($request->hasFile('image_file')) {
            $data['image_path'] = $request->file('image_file')->store('products', 'public');
        }

        // Handle Topper Options (assuming this is handled here or removed)
        $data['topper_options'] = $request->has('topper_options') ? $request->topper_options : null;
        
        unset($data['image_file']); 

        Product::create($data);
        
        return redirect()->route('admin.products.index')
                         ->with('success', 'Product created successfully.');

        
    }

 
    
    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        // Fetch all products for listing context
        $products = Product::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle Image Update
        if ($request->hasFile('image_file')) {
            // Delete old image if it exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            // Store the new image
            $data['image_path'] = $request->file('image_file')->store('products', 'public');
        }

        // Handle Topper Options
        $data['topper_options'] = $request->has('topper_options') ? $request->topper_options : null;

        unset($data['image_file']);
        
        $product->update($data);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product updated successfully.');

        
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Product $product): RedirectResponse
    {
        // This command triggers the Soft Delete (sets deleted_at timestamp)
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product "' . $product->name . '" has been archived (soft deleted).');
    }
}