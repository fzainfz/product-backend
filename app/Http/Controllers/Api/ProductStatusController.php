<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductStatus;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Product Statuses",
 *     description="Operations related to product status"
 * )
 */
class ProductStatusController extends Controller
{
    /**
     * List all statuses
     */
    /**
     * @OA\Get(
     *     path="/api/v1/get-statuses",
     *     summary="Get all statuses",
     *     tags={"Statuses"},
     *     @OA\Response(response=200, description="List of statuses")
     * )
     */
    public function index()
    {
        try {
             $data =ProductStatus::latest()->paginate(5);
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
            Log::error("Error fetching statuses: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch statuses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new status
     */
    /**
     * @OA\Post(
     *     path="/api/v1/store-status",
     *     summary="Create a new status",
     *     tags={"Statuses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Status created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_statuses,name',
            ]);

            $status = ProductStatus::create($validated);

            return response()->json($status, 201);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Error creating status: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to create status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single status
     */
    /**
 * @OA\Get(
 *     path="/api/v1/get-status/{id}",
 *     summary="Get status details by ID",
 *     tags={"Statuses"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Status details"),
 *     @OA\Response(response=404, description="Status not found")
 * )
 */
    public function show($id)
    {
        try {
            $status = ProductStatus::findOrFail($id);
            return response()->json($status, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching status {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Status not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update a status
     */
    /**
     * @OA\Put(
     *     path="/api/v1/update-status/{id}",
     *     summary="Update status",
     *     tags={"Statuses"},
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
     *     @OA\Response(response=200, description="Status updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $status = ProductStatus::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_statuses,name,' . $status->id,
            ]);

            $status->update($validated);

            return response()->json($status, 200);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Error updating status {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a status
     */
     /**
     * @OA\Delete(
     *     path="/api/v1/delete-status/{id}",
     *     summary="Delete status",
     *     tags={"Statuses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Status deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $status = ProductStatus::findOrFail($id);
            $status->delete();

            return response()->json(['message' => 'Status deleted successfully'], 200);

        } catch (\Exception $e) {
            Log::error("Error deleting status {$id}: ".$e->getMessage());
            return response()->json([
                'message' => 'Failed to delete status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
