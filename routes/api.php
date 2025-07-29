<?php

use App\Http\Controllers\Admin\AdminBrandsController;
use App\Http\Controllers\Admin\AdminDriversController;
use App\Http\Controllers\Admin\AdminModelNameController;
use App\Http\Controllers\Admin\AdminsAuthController;
use App\Http\Controllers\Admin\AdminTypesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\User\UserAuthController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\DriverBookingController;
use App\Http\Controllers\Sales\SalesBookingController;
use App\Http\Controllers\User\CarModelRatingController;
use App\Http\Controllers\User\HomePageController;
use App\Http\Controllers\User\LocationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Middleware\Driver;

Route::middleware('lang')->group(function () {
///////////////////////////////Admin Routes////////////////////////////////////
Route::prefix('/admin')->group(function () {
    Route::post('/register', [AdminsAuthController::class, 'register'])->name('admins.register');
    Route::post('/login', [AdminsAuthController::class, 'login'])->name('admins.login');
});
Route::middleware('admin')->prefix('/admin')->group(function () {
    Route::post('/logout', [AdminsAuthController::class, 'logout'])->name('admins.logout');
    //Brands
    Route::get('/Brands',[AdminBrandsController::class,'index']);   
    Route::get('/Brands/{id}',[AdminBrandsController::class,'show']);   
    Route::post('/Brands',[AdminBrandsController::class,'store']);   
    Route::post('/Brands/{id}',[AdminBrandsController::class,'update']);   
    Route::delete('/Brands/{id}',[AdminBrandsController::class,'destroy']);
    Route::post('/Brands/{id}/image', [AdminBrandsController::class, 'updateImage']);

    #Types  
    Route::get('/Brands/{brand}/Types',[AdminTypesController::class,'index']);   
    Route::get('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'show']);   
    Route::post('/Brands/{brand}/Types',[AdminTypesController::class,'store']);   
    Route::post('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'update']);   
    Route::delete('/Brands/{brand}/Types/{id}',[AdminTypesController::class,'destroy']);  
    
    // Model Names CRUD (nested in brand & type)
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names', [AdminModelNameController::class, 'index']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names', [AdminModelNameController::class, 'store']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names/{id}', [AdminModelNameController::class, 'update']);
    Route::delete('/Brands/{brandId}/Types/{typeId}/Model-Names/{id}', [AdminModelNameController::class, 'destroy']);
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names/{id}', [AdminModelNameController::class, 'show']);

    // Models CRUD (nested in brand & type)
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models', [ModelController::class, 'index']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models', [ModelController::class, 'store']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{id}', [ModelController::class, 'update']);
    Route::delete('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{id}', [ModelController::class, 'destroy']);
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{id}', [ModelController::class, 'show'])->name('show-model');
    Route::post('/Models/{id}/image', [ModelController::class, 'updateImage']);

    //cars
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{modelId}/Cars', [CarController::class, 'index']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{modelId}/Cars', [CarController::class, 'store']);
    Route::get('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{modelId}/Cars/{car}', [CarController::class, 'show']);
    Route::post('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{modelId}/Cars/{car}', [CarController::class, 'update']); 
    Route::delete('/Brands/{brandId}/Types/{typeId}/Model-Names/{modelNameId}/Models/{modelId}/Cars/{car}', [CarController::class, 'destroy']);
    
    //Booking
    
    Route::get('/Confirmed-Booking', [BookingController::class, 'ConfirmedBooking']);
    Route::get('/Driver-Assigned-Booking', [BookingController::class, 'DriverAssignedBooking']);
    Route::get('/Completed-Booking', [BookingController::class, 'CompletedBooking']);
    Route::get('/Canceled-Booking', [BookingController::class, 'CanceledBooking']);
    Route::get('/Booking/{id}', [BookingController::class, 'bookingDetails']);
    Route::delete('/Booking/{id}', [BookingController::class, 'destroy']);
    
    Route::get('/Drivers', [AdminDriversController::class, 'getDrivers']);
    Route::post('/Booking/{id}/assign-driver', [AdminDriversController::class, 'assignDriver']);

    Route::get('/Booking/{id}/Cars', [BookingController::class, 'getCars']);
    Route::post('/Booking/{bookingId}/Assign-Car/', [BookingController::class, 'assignCar']);
    Route::post('/booking/{id}/status', [BookingController::class, 'changeStatus']);
    Route::get('/new-bookings', [BookingController::class, 'NewBookings']);

    
//Statistics
    Route::get('/statistics', [StatisticsController::class, 'bookingStatusStats']);
    Route::get('dashboard/statistics', [StatisticsController::class, 'statisticsHome']);

    //Admin Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

});
///////////////////////////////User Routes////////////////////////////////////
Route::prefix('/user')->group(function () {

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [UserAuthController::class, 'verifyCode']);
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword']);
    Route::get('/related', [CarController::class, 'related']);
    Route::post('/Home', [HomePageController::class, 'index']);
    Route::get('/Model/{id}', [HomePageController::class, 'show'])->name('show-details');
    Route::get('/filter-Info', [HomePageController::class, 'filterInfo']);

});


    
///////////////////////////////User-Middleware Routes////////////////////////////////////
Route::middleware('user')->prefix('/user')->group(function () {
    Route::post('/user-locations', [LocationController::class, 'setUserLocation']);
    Route::post('/user-locations/{id}', [LocationController::class, 'updateUserLocation']);
    Route::get('/user-locations', [LocationController::class, 'getUserLocation']);
    Route::get('/active-locations', [LocationController::class, 'getUserActiveLocation']);
    Route::post('/Model/{id}/car-booking', [UserBookingController::class, 'carBooking']);
    Route::post('/Model/{modelId}/car-booking/{id}/payment-method', [UserBookingController::class, 'setPaymentMethod']);
    Route::post('/Model/{modelId}/car-booking/{id}/paymob-info', [UserBookingController::class, 'setPaymobInfo']);
    Route::post('/update-profile', [ProfileController::class, 'updateUserProfile']);
    Route::get('/user-profile', [ProfileController::class, 'userProfile']);    
    Route::get('/booking-list', [ProfileController::class, 'bookingList']);

    Route::post('/Model/{modelId}/rate', [CarModelRatingController::class, 'setRate']);
    Route::delete('/Model/{modelId}/reset-rate', [CarModelRatingController::class, 'resetRate']);

    Route::post('favorites/toggle/{carModel}', [FavoriteController::class, 'toggleFavorite']);
    Route::get('favorites', [FavoriteController::class, 'getFavorites']);

    Route::post('/logout', [UserAuthController::class, 'logout']);
});


Route::prefix('/driver')->group(function () {

    Route::post('/register', [DriverAuthController::class, 'register']);
    Route::post('/login', [DriverAuthController::class, 'login']);
    Route::post('/forgot-password', [DriverAuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [DriverAuthController::class, 'verifyCode']);
    Route::post('/reset-password', [DriverAuthController::class, 'resetPassword']);

});
Route::middleware('driver')->prefix('/driver')->group(function () {
    Route::get('/Completed-Booking', [DriverBookingController::class, 'CompletedBooking']);
    Route::get('/Assigned-Booking', [DriverBookingController::class, 'AssignedBooking']);
    Route::get('/Accepted-Booking', [DriverBookingController::class, 'AcceptedBooking']);

    Route::post('/booking/{id}/status', [DriverBookingController::class, 'changeStatus']); 

    Route::post('/update-Location', [DriverBookingController::class, 'updateLocation']);
    Route::post('/getBestRoute', [DriverBookingController::class, 'getBestRoute']);
    
    Route::get('driver/{id}', [DriverController::class, 'show']);
    Route::post('driver/{id}', [DriverController::class, 'update']);

    Route::post('/logout', [DriverAuthController::class, 'logout']);


        Route::post('/booking-pending/{id}', [DriverBookingController::class, 'carOnTheWay']);

    Route::get('/Confirmed-Booking', [DriverBookingController::class, 'ConfirmedBooking']); 

    Route::get('/Canceled-Booking', [DriverBookingController::class, 'CanceledBooking']);

    Route::get('/booking/{id}', [DriverBookingController::class, 'bookingDetails']); 


    
});

// ///////////////////////////////Sales Routes////////////////////////////////////
// Route::prefix('/sales')->middleware('sales')->group(function () {
//     Route::get('/booking/confirmed', [SalesBookingController::class, 'ConfirmedBooking']);
//     Route::get('/booking/completed', [SalesBookingController::class, 'CompletedBooking']);
//     Route::get('/booking/assigned', [SalesBookingController::class, 'AssignedBooking']);
//     Route::get('/booking/canceled', [SalesBookingController::class, 'CanceledBooking']);

//     Route::get('/booking/{bookingId}/cars', [SalesBookingController::class, 'getCars']);
//     Route::post('/booking/{bookingId}/assign-car/{carId}', [SalesBookingController::class, 'assignCar']);

//     // ⬇ روتات SalesController
//     Route::post('/booking/{id}/mark-assigned', [SalesController::class, 'markAsAssigned']);
//     Route::post('/booking/{id}/change-status', [SalesController::class, 'changeStatus']);
//     Route::get('/booking/{id}', [SalesController::class, 'bookingDetails']);
// });

});