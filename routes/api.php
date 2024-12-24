<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

Route::post('/register', [APIController::class, 'register']);
Route::post('/login', [APIController::class, 'login']);
Route::post('/generate-otp', [APIController::class, 'generateOtp']);
Route::post('/reset-password-with-otp', [APIController::class, 'resetPasswordWithOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/update-profile', [APIController::class, 'updateProfile']);
    Route::post('/table-type', [APIController::class, 'createTableType']);
    Route::post('/sitting-table', [APIController::class, 'createSittingTable']);
    Route::post('/payment-plan', [APIController::class, 'createPaymentPlan']);
    Route::post('/reservation', [APIController::class, 'createReservation']);
    Route::put('/table-type/{id}', [APIController::class, 'updateTableType']);
    Route::put('/sitting-table/{id}', [APIController::class, 'updateSittingTable']);
    Route::put('/payment-plan/{id}', [APIController::class, 'updatePaymentPlan']);
    Route::put('/reservation/{id}', [APIController::class, 'updateReservation']);
    Route::delete('/table-type/{id}', [APIController::class, 'deleteTableType']);
    Route::delete('/sitting-table/{id}', [APIController::class, 'deleteSittingTable']);
    Route::delete('/payment-plan/{id}', [APIController::class, 'deletePaymentPlan']);
    Route::delete('/reservation/{id}', [APIController::class, 'deleteReservation']);
    Route::get('/tables/{status}', [APIController::class, 'getTablesByReservationStatus']);

    Route::post('/reservation-payment', [APIController::class, 'createReservationPayment']);
    Route::put('/reservation-payment/{id}', [APIController::class, 'updateReservationPayment']);
    Route::get('/reservation-payment', [APIController::class, 'getReservationPayment']); // Get all records
    Route::get('/reservation-payment/{id}', [APIController::class, 'getReservationPayment']); // Get by ReservationID
    Route::delete('/reservation-payment/{id}', [APIController::class, 'deleteReservationPayment']);

    Route::post('/brands', [APIController::class, 'createBrand']);
    Route::put('/brands/{id}', [APIController::class, 'updateBrand']);
    Route::delete('/brands/{id}', [APIController::class, 'deleteBrand']); // Delete Brand
    Route::post('/category', [APIController::class, 'createCategory']);
    Route::put('/category/{id}', [APIController::class, 'updateCategory']);
    Route::delete('/category/{id}', [APIController::class, 'deleteCategory']); // Delete Category
    Route::post('/subcategory', [APIController::class, 'createSubCategory']);
    Route::put('/subcategory/{id}', [APIController::class, 'updateSubCategory']);
    Route::delete('/subcategory/{id}', [APIController::class, 'deleteSubCategory']); // Delete SubCategory
    Route::post('/subsubcategory', [APIController::class, 'createSubSubCategory']);
    Route::put('/subsubcategory/{id}', [APIController::class, 'updateSubSubCategory']);
    Route::delete('/subsubcategory/{id}', [APIController::class, 'deleteSubSubCategory']); // Delete SubSubCategory
    Route::post('/products', [APIController::class, 'createProduct']);
    Route::put('/products/{id}', [APIController::class, 'updateProduct']);
    Route::get('/products', [APIController::class, 'getProducts']); // Fetch all products
    Route::get('/products/{id}', [APIController::class, 'getProductById']); // Fetch product by ID
    Route::delete('/products/{id}', [APIController::class, 'deleteProduct']); // Delete Product

    Route::post('/allpayments', [APIController::class, 'createAllPayment']);
    Route::put('/allpayments/{id}', [APIController::class, 'updateAllPayment']);
    Route::get('/allpayments', [APIController::class, 'getAllPayments']); // Fetch all records
    Route::get('/allpayments/{id}', [APIController::class, 'getAllPayments']); // Fetch a specific record by ID
    Route::delete('/allpayments/{id}', [APIController::class, 'deleteAllPayment']);

    Route::post('/vendors', [APIController::class, 'createVendor']);
    Route::post('/logout', [APIController::class, 'logout']);
});