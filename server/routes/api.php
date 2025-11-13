<?php

use App\Http\Controllers\InstrumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


//endpoint
Route::get('/', function(){
    return 'API';
});

Route::get('instruments', [InstrumentController::class, 'index']);
Route::get('instruments/{id}', [InstrumentController::class, 'show']);
Route::post('instruments', [InstrumentController::class, 'store']);
Route::delete('instruments/{id}', [InstrumentController::class, 'destroy']);
Route::patch('instruments/{id}', [InstrumentController::class, 'update']);