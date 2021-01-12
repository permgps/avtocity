<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TemplateController;

Route::get('un', function () {
    return response(null,401);
})->name('un');

Route::group(['prefix' => 'auth'], function() {
    Route::post('signin',[AuthController::class, 'signin']);
});

Route::group(['prefix' => 'auth','middleware' => ['auth']], function() {
    Route::get('logout',[AuthController::class, 'logout']);
    Route::get('me',[AuthController::class, 'me']);
});

Route::group(['prefix' => 'car','middleware' => ['auth']], function() {
    Route::get('load',[CarController::class, 'load']);
    Route::post('save',[CarController::class, 'save']);
    Route::post('delete',[CarController::class, 'delete']);
});

Route::group(['prefix' => 'driver','middleware' => ['auth']], function() {
    Route::get('load',[DriverController::class, 'load']);
    Route::post('save',[DriverController::class, 'save']);
    Route::post('edit',[DriverController::class, 'edit']);
    Route::post('delete',[DriverController::class, 'delete']);
    Route::post('feel',[DriverController::class, 'feel']);
    Route::get('report',[DriverController::class, 'report']);
});

Route::group(['prefix' => 'student','middleware' => ['auth']], function() {
    Route::get('load',[StudentController::class, 'load']);
    Route::post('save',[StudentController::class, 'save']);
    Route::post('edit',[StudentController::class, 'edit']);
    Route::post('delete',[StudentController::class, 'delete']);
});

Route::group(['prefix' => 'template','middleware' => ['auth']], function() {
    Route::get('load',[TemplateController::class, 'load']);
    Route::post('save',[TemplateController::class, 'save']);
    Route::post('delete',[TemplateController::class, 'delete']);
});

Route::group(['prefix' => 'event','middleware' => ['auth']], function() {
    Route::get('load',[EventController::class, 'load']);
    Route::post('save',[EventController::class, 'save']);
    Route::post('delete',[EventController::class, 'delete']);
    Route::post('clearstudent',[EventController::class, 'clearstudent']);
    Route::post('join',[EventController::class, 'join']);
});

Route::group(['prefix' => 'payment','middleware' => ['auth']], function() {
    Route::get('load',[PaymentController::class, 'load']);
    Route::post('save',[PaymentController::class, 'save']);
    Route::post('delete',[PaymentController::class, 'delete']);
});
