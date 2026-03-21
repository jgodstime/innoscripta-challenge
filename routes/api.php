<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'getArticle']); // * Regular route to get articles based on query parameters (category, author, source) without authentication
        Route::get('/auth', [ArticleController::class, 'getArticle'])->middleware('auth:sanctum'); // * only authenicated user can access this route because it gets user preferred categories, sources and authors
        Route::get('/{articleId}', [ArticleController::class, 'showArticle']);
    });

    Route::get('categories', [CategoryController::class, 'getCategory']);
    Route::get('authors', [AuthorController::class, 'getAuthor']);
    Route::get('sources', [SourceController::class, 'getSource']);

});
