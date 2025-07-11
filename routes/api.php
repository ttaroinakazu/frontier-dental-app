<?php

use App\Http\Controllers\API\V1\Auth\LoginController;
use App\Http\Controllers\API\V1\Auth\LogoutController;
use App\Http\Controllers\API\V1\Auth\RegisterController;
use App\Http\Controllers\API\V1\Auth\ShowUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('json.response')->group(function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);


});


Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\API\V1',
    'middleware' => ['json.response', 'auth:sanctum']  // Fix: array of middleware
], function () {
    // Authentication routes
    Route::post('logout', LogoutController::class);
    Route::get('user', ShowUserController::class);


    Route::apiResource('products', App\Http\Controllers\API\V1\ProductController::class);
    Route::apiResource('wishlist', App\Http\Controllers\API\V1\WishlistController::class, ['only' => ['index', 'store']]);
    Route::delete('wishlist/{product}', [App\Http\Controllers\API\V1\WishlistController::class, 'destroy']);
});
