<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Operations related to products"
 * )
 */
class ProductController extends Controller
{
    /**
     * List products with category, status, and pagination
     */
    /**
     * @OA\Get(
     *     path="/api/v1/get-products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'status', 'media']);

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%$search%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('status', fn($q) => $q->where('name', 'like', "%$search%"));
            }

            if ($request->has('product_category_id')) {
                $query->where('product_category_id', $request->get('product_category_id'));
            }

            if ($request->has('product_status_id')) {
                $query->where('product_status_id', $request->get('product_status_id'));
            }

            $products = $query->latest()->paginate(10);


            return response()->json([
                'data' => $products->items(), // array of products
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching products: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new product with optional image
     */
    /**
     * @OA\Post(
     *     path="/api/v1/store-product",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","category_id","status_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="status_id", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'product_category_id' => 'required|exists:product_categories,id',
                'product_status_id' => 'required|exists:product_statuses,id',
                'images.*' => 'nullable|image|max:5120',  // 5MB max
            ]);

            $product = Product::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('products');
                }
            }

            return response()->json($product->load('category', 'status', 'media'), 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error creating product: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single product
     */
    /**
     * @OA\Get(
     *     path="/api/v1/get-product/{id}",
     *     summary="Get product details by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product details"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'status', 'media'])->findOrFail($id);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching product {$id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update a product
     */
    /**
     * @OA\Put(
     *     path="/api/v1/update-product/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="status_id", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'price' => 'sometimes|numeric',
                'product_category_id' => 'sometimes|exists:product_categories,id',
                'product_status_id' => 'sometimes|exists:product_statuses,id',
               'images.*' => 'nullable|image|max:5120', 
            ]);

            $product->update($validated);
            if ($request->hasFile('images')) {
                $product->clearMediaCollection('products');
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('products');
                }
            }

            return response()->json($product->load('category', 'status', 'media'), 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error updating product {$id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/delete-product/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->clearMediaCollection('products'); // remove image files
            $product->delete();

            return response()->json(['message' => 'Product deleted'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting product {$id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
