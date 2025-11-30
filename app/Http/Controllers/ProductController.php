<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    // Eager Loading: Kukunin ang lahat ng products at isasama 
    // ang related na data mula sa 'category' at 'user' relationships.
    $products = Product::with('category', 'user')->get(); 
    
    // Optional: Maaari mo ring gamitin ang paginate(10) para sa mas malaking dataset

    // Ibalik ang data bilang isang JSON response
    return response()->json([
        'status' => 'success',
        'products' => $products
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation (Tinitiyak na kumpleto at tama ang data)
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        // Tinitiyak na may existing na ID sa categories table
        'category_id' => 'required|exists:categories,id', 
    ]);

    // 2. I-set ang User ID
    // DAHIL WALA PA TAYONG AUTHENTICATION: pansamantala, gagamitin natin ang ID ng unang user.
    $validatedData['user_id'] = User::first()->id; 

    // 3. I-save ang Product
    $product = Product::create($validatedData);

    // 4. Magbalik ng successful JSON response
    return response()->json([
        'status' => 'success',
        'message' => 'Product successfully created.',
        'product' => $product
    ], 201); // 201 Created Status
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product) // Route Model Binding
{
    // Awtomatikong hinanap ng Laravel ang produkto.
    // Hindi na kailangan ang Product::find($id) o ang if check.

    return response()->json([
        'status' => 'success',
        'product' => $product
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateProductRequest $request, Product $product) 
    {
        // --- DEBUGGING CODE START ---
        $loggedInUserId = auth()->id();
        $productOwnerId = $product->user_id;

        // I-dump ang User IDs para makita kung pareho ba sila
        // Gamitin ang dd() para huminto ang execution at ipakita ang output
        // dd("Logged In User ID: " . $loggedInUserId, "Product Owner ID: " . $productOwnerId);
        
        // Maaari mo ring i-comment ang dd() at gamitin ang log:
        \Log::info("UPDATE ATTEMPT | Logged In ID: {$loggedInUserId} | Product Owner ID: {$productOwnerId} | Product ID: {$product->id}");
        // --- DEBUGGING CODE END ---

        // 1. Authorization Check (Ito ang nagbibigay ng 403)
        if ($loggedInUserId !== $productOwnerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this product.'
            ], 403); // 403 Forbidden
        }

        // 2. Validation and Update
        $validatedData = $request->validated();
        $product->fill($validatedData);
        $product->save();

        // 3. Response
        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully updated.',
            'product' => $product 
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
{
    // TANDAAN: Dahil Route Model Binding (Product $product) ang ginamit,
    // automatic na 404 Not Found ang response kung walang product na nahanap.

    // 1. I-delete ang Product
    $product->delete();

    // 2. Magbalik ng successful JSON response (204 No Content ang karaniwan)
    // Pero gagamitin natin ang 200 OK para magbigay ng message.
    return response()->json([
        'status' => 'success',
        'message' => 'Product successfully deleted.'
    ], 200);
    }
}
