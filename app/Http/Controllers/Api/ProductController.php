<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'category'];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection($this->loadRelationships(Product::query())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();

        $product = new Product;
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->price = $validatedData['price'];
        $product->category_id = $validatedData['category_id'];
        $product->user_id = $request->user()->id;
        $product->save();

        return new ProductResource($this->loadRelationships($product));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($this->loadRelationships($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if ($request->user()->id != $product->user->id) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $product->update(
            $request->validate([
                'name' => 'sometimes|string|max:50',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric',
                'category_id' => 'sometimes|exists:categories,id',
            ])
        );

        return new ProductResource($this->loadRelationships($product));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        if ($request->user()->id != $product->user->id) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $product->delete();

        return response(status: 204);
    }
}
