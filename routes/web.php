<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
 
Route::get('/even-numbers', function () {
    return view('even_numbers');
});
 
Route::get('/prime-numbers', function () {
    return view('welcome');
});

Route::get('/multiplication', function () {
    return view('multiplication  table');
});
