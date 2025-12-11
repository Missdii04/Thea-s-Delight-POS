<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the 'role:admin' middleware on the route
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // $this->product is automatically available for updates (route model binding)
        return [
            'name' => ['required', 'string', 'max:255'],  
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->product)],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            
            // Cake Specific Fields
            'category' => ['required', 'string', 'max:255'], 
            'size' => ['nullable', 'string', 'max:50'], 
            'flavor' => ['nullable', 'string', 'max:255'], 
            

            // Image Upload Validation
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ];
    }
}