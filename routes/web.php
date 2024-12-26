<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ProductController::class, 'index'])->middleware('auth')->name("products.index");
Route::get('/products/{id}/buy', [ProductController::class, 'buy'])->name('products.buy')->middleware('auth');
Route::post('/products/{id}/charge', [ProductController::class, 'charge'])->name('products.charge')->middleware('auth');

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
