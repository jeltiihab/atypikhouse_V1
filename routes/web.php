<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function (Request $request) {

    return view('emailVerifyin',[
        "url" => URL::previous()
    ]);
})->name('login');


Route::get('/email/verify', function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(['error'=>"email must verified"],400);
})->middleware('auth:sanctum')->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = \App\Models\User::findOrFail( (int)$request->route('id') );
    $user->markEmailAsVerified();
    event(new Verified($user));
    return redirect("https://f2i-cw1-ij-hc-nag.fr/") ;
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');



Route::view('/payment','payment');
