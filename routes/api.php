<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('register',[userController::class,'register']);
Route::post('login',[userController::class,'login']);
Route::post('logout',[userController::class,'logout'])->middleware('auth:sanctum');

Route::get('get',[ApartmentController::class,'getAllApartment']);

Route::get('get_custom_apartment',[ApartmentController::class,'getCustomApartment']);
Route::get('get/{id}',[ApartmentController::class,'getDetailsApartment']);
Route::post('store',[ApartmentController::class,'storeApartment'])->middleware('auth:sanctum');
Route::post('reserve/{id}',[ReservationController::class,'reserveApartment'])->middleware('auth:sanctum');
Route::put('/reservations/{id}', [ReservationController::class, 'updateReservation'])->middleware('auth:sanctum');
Route::get('userApartments', [ApartmentController::class, 'getUserApartments'])->middleware('auth:sanctum');
Route::delete('/apartments/{id}', [ApartmentController::class, 'deleteApartment'])->middleware('auth:sanctum');
