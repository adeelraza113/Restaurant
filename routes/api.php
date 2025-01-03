<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

Route::post('/register', [APIController::class, 'register']);
Route::post('/login', [APIController::class, 'login']);
Route::post('/generate-otp', [APIController::class, 'generateOtp']);
Route::post('/reset-password-with-otp', [APIController::class, 'resetPasswordWithOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/table-type', [APIController::class, 'createTableType']);
    Route::post('/sitting-table', [APIController::class, 'createSittingTable']);
    Route::post('/payment-plan', [APIController::class, 'createPaymentPlan']);
    Route::post('/reservation', [APIController::class, 'createReservation']);
    Route::put('/table-type', [APIController::class, 'updateTableType']);
    Route::put('/sitting-table', [APIController::class, 'updateSittingTable']);
    Route::put('/payment-plan', [APIController::class, 'updatePaymentPlan']);
    Route::put('/reservation', [APIController::class, 'updateReservation']);
    Route::delete('/table-type', [APIController::class, 'deleteTableType']);
    Route::delete('/sitting-table', [APIController::class, 'deleteSittingTable']);
    Route::delete('/payment-plan', [APIController::class, 'deletePaymentPlan']);
    Route::delete('/reservation', [APIController::class, 'deleteReservation']);
    Route::get('/tables', [APIController::class, 'getTablesByReservationStatus']);

    Route::post('/reservation-payment', [APIController::class, 'createReservationPayment']);
    Route::put('/reservation-payment', [APIController::class, 'updateReservationPayment']);
    Route::get('/reservation-payment', [APIController::class, 'getReservationPayment']);
    Route::delete('/reservation-payment', [APIController::class, 'deleteReservationPayment']);

    Route::post('/brands', [APIController::class, 'createBrand']);
    Route::put('/brands', [APIController::class, 'updateBrand']);
    Route::delete('/brands', [APIController::class, 'deleteBrand']);
    Route::post('/category', [APIController::class, 'createCategory']);
    Route::put('/category', [APIController::class, 'updateCategory']);
    Route::delete('/category', [APIController::class, 'deleteCategory']);
    Route::post('/subcategory', [APIController::class, 'createSubCategory']);
    Route::put('/subcategory', [APIController::class, 'updateSubCategory']);
    Route::delete('/subcategory', [APIController::class, 'deleteSubCategory']); 
    Route::post('/subsubcategory', [APIController::class, 'createSubSubCategory']);
    Route::put('/subsubcategory', [APIController::class, 'updateSubSubCategory']);
    Route::delete('/subsubcategory', [APIController::class, 'deleteSubSubCategory']); 
    Route::post('/products', [APIController::class, 'createProduct']);
    Route::put('/products', [APIController::class, 'updateProduct']);
    Route::get('/products', [APIController::class, 'searchProducts']); 
    Route::delete('/products', [APIController::class, 'deleteProduct']);

    Route::post('/allpayments', [APIController::class, 'createAllPayment']);
    Route::put('/allpayments', [APIController::class, 'updateAllPayment']);
    Route::get('/allpayments', [APIController::class, 'getAllPayments']); 
    Route::delete('/allpayments', [APIController::class, 'deleteAllPayment']);

    Route::post('/vendors', [APIController::class, 'createVendor']);
    Route::put('/vendors', [APIController::class, 'updateVendor']);
    Route::get('/vendors', [APIController::class, 'getVendorDetails']);
    Route::delete('/vendors', [APIController::class, 'deleteVendor']);

    Route::post('/purchase-master', [APIController::class, 'createPurchaseMaster']);
    Route::put('/purchase-master/{id}', [APIController::class, 'updatePurchaseMaster']);
    Route::get('/purchase-master', [APIController::class, 'getPurchaseMasters']);
    Route::delete('/purchase-master', [APIController::class, 'deletePurchaseMaster']);

    Route::get('/view-products', [APIController::class, 'getAllProducts']);
    Route::get('/view-reservation-payment', [APIController::class, 'getTableDetails']);
    Route::get('/view-purchases', [APIController::class, 'getPurchases']);

    Route::post('/purchase-detail', [APIController::class, 'createPurchaseDetail']);
    Route::put('/purchase-detail', [APIController::class, 'updatePurchaseDetail']);
    Route::get('/purchase-detail', [APIController::class, 'getPurchaseDetails']);
    Route::delete('/purchase-detail', [APIController::class, 'deletePurchaseDetail']);

    Route::post('/logout', [APIController::class, 'logout']);
});