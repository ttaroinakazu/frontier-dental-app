<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\DeleteWishlistProductRequest;
use App\Http\Requests\API\V1\StoreWishlistRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        // Get the authenticated user
        $user = $request->user() ?? Auth::guard('sanctum')->user();

        // get the user's wishlists with products
        $wishlist = $user->wishlist()->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'No wishlist found'
            ], Response::HTTP_NOT_FOUND);
        }


        $paginatedProducts = $wishlist->products()
            ->paginate($perPage);

        return response()->json([
            'wishlist' => [
                'id' => $wishlist->id,
                'name' => $wishlist->name,
                'user_id' => $wishlist->user_id,
                'created_at' => $wishlist->created_at,
                'updated_at' => $wishlist->updated_at,
                'products' => [
                    'data' => ProductResource::collection($paginatedProducts),
                ],
            ]
        ]);

    }
    public function store(StoreWishlistRequest $request)
    {
        // Get the authenticated user
        $user = $request->user() ?? Auth::guard('sanctum')->user();

        $data = $request->validated();

        // UpdateOrCreate the wishlist
        $wishlist = $user->wishlist()->updateOrCreate(
            ['user_id' => $user->id], // conditions for finding existing wishlist
            [
                'name' => $data['name'] ?? $user->name . "'s Wishlist",
                'user_id' => $user->id
            ]
        );


        /// If products are provided, attach them to the wishlist (without duplicates)
        if (isset($data['products'])) {
            $productIds = collect($data['products'])->pluck('id')->toArray();
            $wishlist->products()->syncWithoutDetaching($productIds);
        }

        return response()->json([
            'message' => 'Wishlist updated successfully',
        ], 201);
    }

    public function destroy(DeleteWishlistProductRequest $request
        , $product)
    {
        // Get the authenticated user
        $user = $request->user() ?? Auth::guard('sanctum')->user();

        $wishlist = $user->wishlist;
        if (!$wishlist) {
            return response()->json(['message' => 'Wishlist not found'], 404);
        }

        $detached = $wishlist->products()->detach($product);

        if ($detached) {
            return response()->json(['message' => 'Product removed from wishlist'], 200);
        }

        return response()->json(['message' => 'Product not found in wishlist'], 404);
    }


}
