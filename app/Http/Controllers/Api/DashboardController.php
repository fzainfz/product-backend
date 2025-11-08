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
class DashboardController extends Controller
{

    /**
     * Dashboard Data
     */
    /**
     * @OA\Get(
     *     path="/api/v1/get-dashboard",
     *     summary="Get Dashboard Data",
     *     tags={"Dashboard"},
     *     @OA\Response(response=200, description="Dashboard Data")
     * )
     */
    public function dashboard()
    {
        try {
            // Total products
            $totalProducts = Product::count();

            // Count products by status dynamically
            $statusCounts = Product::with('status')
                ->get()
                ->groupBy(fn($p) => $p->status->name ?? 'Unknown')
                ->map(fn($group) => count($group))
                ->toArray();

            // Total categories
            $totalCategories = ProductCategory::count();

            // Products by category using relationship
            $productsByCategory = ProductCategory::withCount('products')
                ->get()
                ->map(fn($cat) => [
                    'name' => $cat->name,
                    'count' => $cat->products_count,
                ]);

            return response()->json([
                'success' => true,
                'totalProducts' => $totalProducts,
                'statusCounts' => $statusCounts, // dynamic statuses
                'totalCategories' => $totalCategories,
                'productsByCategory' => $productsByCategory,
            ]);
        } catch (\Exception $e) {
            // Log the error if needed
            \Log::error('Dashboard API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
