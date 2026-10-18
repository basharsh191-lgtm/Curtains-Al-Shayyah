<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route ::get('wolcome', [WelcomeController :: class , 'fun']);
Route ::post('user', [UserController :: class , 'index']);
