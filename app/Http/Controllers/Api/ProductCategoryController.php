<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Product Categories",
 *     description="Operations related to product categories"
 * )
 */
class ProductCategoryController extends Controller
{
    /**
     * List all categories
     */
     /**
     * @OA\Get(
     *     path="/api/v1/get-categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function index()
    {
        try {
            $data = ProductCategory::latest()->paginate(perPage: 5);
            return response()->json([
            'data' => $data->items(), // array of products
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ],
        ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching categories: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new category
     */
     /**
     * @OA\Post(
     *     path="/api/v1/store-category",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created successfully")
     * )
     */
    public function store(Request $request)
    {
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name',
            ]);

          
            $category = ProductCategory::create($validated);

            return response()->json($category, 201);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Error creating category: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  /**
 * @OA\Get(
 *     path="/api/v1/get-category/{id}",
 *     summary="Get category details by ID",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Category details"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 */
    public function show($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching category {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Category not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update a category
     */
    /**
     * @OA\Put(
     *     path="/api/v1/update-category/{id}",
     *     summary="Update category",
     *     tags={"Categories"},
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
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $category = ProductCategory::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            ]);

            $category->update($validated);

            return response()->json($category, 200);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Error updating category {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a category
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/delete-category/{id}",
     *     summary="Delete category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Category deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            $category->delete();

            return response()->json(['message' => 'Category deleted successfully'], 200);

        } catch (\Exception $e) {
            Log::error("Error deleting category {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
