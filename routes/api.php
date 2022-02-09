<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    //API routes for login user
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);


    Route::get('/properties/three-last', [PropertyController::class, 'propertyThreeLast']);

    Route::group(['middleware' => ['auth:sanctum',"verified"]], function () {

    Route::post('/reservations/check',[ReservationController::class,'check']);
    Route::get('/reservations/confirmation/{orderID}',[ReservationController::class,"approvePayment"])->name('order.capture')->middleware("signed");

    Route::post('/reservations',[ReservationController::class,'store'])->name('order.create')->middleware('signed');
    Route::get('/reservations/user-loc',[ReservationController::class, 'reservationByUserLoc']);
    Route::get('/reservations/user-prop',[ReservationController::class, 'reservationByUserProp']);
    Route::post('/equipementANDAttributesByCategory',[CategoryController::class, 'equipementANDAttributesByCategory']);

    Route::get('/properties-limit',[PropertyController::class, 'propertiesLimit']);

    // Resources Routes
    Route::apiResource("reservations" ,ReservationController::class,["except"=>["store"]]);
    Route::get('/properties/user',[PropertyController::class, 'propertiesByUser']);

    Route::apiResources([
        "properties" => PropertyController::class,
        "categories" => CategoryController::class
    ]);


    // Custom API routes

    // API routes for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});
