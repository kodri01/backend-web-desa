<?php

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;

//route login
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function () {
    //logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
});


//group route with prefix "admin"
Route::prefix('admin')->group(function () {
    //group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {
        //dashboard
        Route::get('/dashboard', App\Http\Controllers\Api\Admin\DashboardController::class);

        //permissions
        Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all'])
            ->middleware('permission:permissions.index');
        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'index'])
            ->middleware('permission:permissions.index');

        //roles
        Route::get('/roles/all', [\App\Http\Controllers\Api\Admin\RoleController::class, 'all'])
            ->middleware('permission:roles.index');
        Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class)
            ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        // users
        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class)
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // categories
        Route::get('/categories/all', [App\Http\Controllers\Api\Admin\CategoryController::class, 'all'])
            ->middleware('permission:categories.index');
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class)
            ->middleware('permission:categories.index|categories.store|categories.update|categories.delete');

        // posts
        Route::apiResource('/posts', App\Http\Controllers\Api\Admin\PostController::class)
            ->middleware('permission:posts.index|posts.store|posts.update|posts.delet');

        // product
        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class)
            ->middleware('permission:products.index|products.store|products.update|products.delete');

        // pages
        Route::apiResource('/pages', App\Http\Controllers\Api\Admin\PageController::class)
            ->middleware('permission:pages.index|pages.store|pages.update|pages.delete');

        // photos
        Route::apiResource('/photos', App\Http\Controllers\Api\Admin\PhotoController::class, ['except' => ['show', 'update', 'create']])
            ->middleware('permission:photos.index|photos.store|photos.delete');

        // slider
        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['show', 'update', 'create']])
            ->middleware('permission:sliders.index|sliders.store|sliders.delete');

        // aparaturs
        Route::apiResource('/aparaturs', App\Http\Controllers\Api\Admin\AparaturController::class)
            ->middleware('permission:aparaturs.index|aparaturs.store|aparaturs.update|aparaturs.delete');
    });
});


//group route with prefix "public"
Route::prefix('public')->group(function () {
    // Post
    Route::get('/posts', [App\Http\Controllers\Api\Public\PostController::class, 'index']);
    Route::get('/posts/{slug}', [App\Http\Controllers\Api\Public\PostController::class, 'show']);
    Route::get('/posts_home', [App\Http\Controllers\Api\Public\PostController::class, 'homePage']);

    //product
    Route::get('/products', [App\Http\Controllers\Api\Public\ProductController::class, 'index']);
    Route::get('/products/{slug}', [App\Http\Controllers\Api\Public\ProductController::class, 'show']);
    Route::get('/products_home', [App\Http\Controllers\Api\Public\ProductController::class, 'homePage']);

    //pages
    Route::get('/pages', [App\Http\Controllers\Api\Public\PageController::class, 'index']);
    Route::get('/pages/{slug}', [App\Http\Controllers\Api\Public\PageController::class, 'show']);

    //aparaturs
    Route::get('/aparaturs', [App\Http\Controllers\Api\Public\AparaturController::class, 'index']);

    //photo
    Route::get('/photos', [App\Http\Controllers\Api\Public\PhotoController::class, 'index']);

    //slider
    Route::get('/sliders', [App\Http\Controllers\Api\Public\SliderController::class, 'index']);
});
