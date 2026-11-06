<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::resource('books',BookController::class)->only(['index','show']);

Route::resource('books.reviews',ReviewsController::class)->scoped()->only(['create']);

Route::resource('books.reviews',ReviewsController::class)->scoped()->only(['store'])->middleware('throttle:reviews');
