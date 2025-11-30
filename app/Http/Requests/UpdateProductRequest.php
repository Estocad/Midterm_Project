<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       // Tiyakin na ang user ay naka-authenticate bago mag-update.
    return true; 
}

public function rules(): array
{
    // Ginagamit ang 'sometimes' para payagan ang partial updates (PATCH)
    return [
        'name' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|required|numeric|min:0',
        'category_id' => 'sometimes|nullable|exists:categories,id', 
    ];
    }
}
