<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

Route::post('/register', [APIController::class, 'register']);
Route::post('/login', [APIController::class, 'login']);
Route::post('/generate-otp', [APIController::class, 'generateOtp']);
Route::post('/reset-password-with-otp', [APIController::class, 'resetPasswordWithOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/table-types', [APIController::class, 'getTableTypes']);
    Route::post('/update-profile', [APIController::class, 'updateProfile']);
    Route::post('/table-type', [APIController::class, 'createTableType']);
    Route::post('/sitting-table', [APIController::class, 'createSittingTable']);
   Route::get('/sitting-table', [APIController::class, 'getSittingTables']);
    Route::post('/payment-plan', [APIController::class, 'createPaymentPlan']);
    Route::get('/payment-plan', [APIController::class, 'getPaymentPlans']);
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
    Route::get('/brands', [APIController::class, 'getBrands']);
    Route::get('/category', [APIController::class, 'getCategories']);
    Route::get('/subcategory', [APIController::class, 'getSubCategories']);
    Route::get('/subsubcategory', [APIController::class, 'getSubSubCategories']);
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

    Route::post('/purchase', [APIController::class, 'createPurchase']);
    Route::put('/purchase', [APIController::class, 'updatePurchase']);
    Route::get('/purchase', [APIController::class, 'getPurchase']);
    Route::delete('/purchase', [APIController::class, 'deletePurchase']);
    
    Route::get('/view-products', [APIController::class, 'getAllProducts']);
    Route::get('/view-reservation-payment', [APIController::class, 'getTableDetails']);
    Route::get('/view-purchases', [APIController::class, 'getPurchases']);

    
    Route::post('/cart', [APIController::class, 'createCart']);
    Route::put('/cart', [APIController::class, 'updateCart']);
    Route::get('/cart', [APIController::class, 'getCart']);
    Route::delete('/cart', [APIController::class, 'deleteCart']);

    Route::get('/reservation', [APIController::class, 'getReservations']);
    Route::put('/purchase', [APIController::class, 'updatePurchaseLock']);
    Route::put('/purchase', [APIController::class, 'updateIssuedToStore']);


    Route::post('/logout', [APIController::class, 'logout']);
});