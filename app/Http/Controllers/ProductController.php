<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UpdateProductRequest;
use App\Helpers\XmlHelper;

class ProductController extends Controller
{
    /**
     * Handle response format (JSON or XML)
     */
    private function formatResponse($data, $status = 200, $headers = [])
    {
        $request = request();
        
        if (XmlHelper::wantsXml($request)) {
            return XmlHelper::toXml($data, 'response', $status, $headers);
        }
        
        return response()->json($data, $status, $headers);
    }

    /**
     * Parse request data based on content type
     */
    private function parseRequestData(Request $request)
    {
        if (XmlHelper::isXml($request)) {
            $xmlContent = $request->getContent();
            return XmlHelper::toArray($xmlContent);
        }
        
        return $request->all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // ADD Request parameter
    {
        $products = Product::with('category', 'user')->get(); 
        
        return $this->formatResponse([ // CHANGE to formatResponse
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => [ // ADD data wrapper
                'products' => $products
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request); // ADD this
        $request->merge($requestData); // ADD this
        
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id', 
        ]);

        // Set user ID
        $validatedData['user_id'] = auth()->id() ?? User::first()->id; // ADD fallback

        // Create product
        $product = Product::create($validatedData);

        return $this->formatResponse([ // CHANGE to formatResponse
            'status' => 'success',
            'message' => 'Product successfully created.',
            'data' => [ // ADD data wrapper
                'product' => $product
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product) // ADD Request parameter
    {
        // Load relationships
        $product->load('category', 'user');
        
        return $this->formatResponse([ // CHANGE to formatResponse
            'status' => 'success',
            'message' => 'Product retrieved successfully',
            'data' => [ // ADD data wrapper
                'product' => $product
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product) 
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request); // ADD this
        $request->merge($requestData); // ADD this
        
        // Debugging
        $loggedInUserId = auth()->id();
        $productOwnerId = $product->user_id;
        
        \Log::info("UPDATE ATTEMPT | Logged In ID: {$loggedInUserId} | Product Owner ID: {$productOwnerId} | Product ID: {$product->id}");

        // Authorization Check
        if ($loggedInUserId !== $productOwnerId) {
            return $this->formatResponse([ // CHANGE to formatResponse
                'status' => 'error',
                'message' => 'You are not authorized to update this product.'
            ], 403);
        }

        // Validate using Form Request
        $validatedData = $request->validated();
        $product->fill($validatedData);
        $product->save();

        return $this->formatResponse([ // CHANGE to formatResponse
            'status' => 'success',
            'message' => 'Product successfully updated.',
            'data' => [ // ADD data wrapper
                'product' => $product 
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product) // ADD Request parameter
    {
        // Authorization check
        $loggedInUserId = auth()->id();
        $productOwnerId = $product->user_id;
        
        if ($loggedInUserId !== $productOwnerId) {
            return $this->formatResponse([ // CHANGE to formatResponse
                'status' => 'error',
                'message' => 'You are not authorized to delete this product.'
            ], 403);
        }

        $product->delete();

        return $this->formatResponse([ // CHANGE to formatResponse
            'status' => 'success',
            'message' => 'Product successfully deleted.'
        ]);
    }
}