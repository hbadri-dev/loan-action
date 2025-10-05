<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\Auth\UnifiedOTPController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/home', function () {
    return view('welcome');
});

// Login route alias for authentication middleware
Route::get('/login', function () {
    return redirect()->route('unified.otp.login');
})->name('login');

// Admin Authentication (Traditional Breeze routes)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin Panel Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

    // Auction Management
    Route::resource('auctions', \App\Http\Controllers\Admin\AuctionController::class);
    Route::post('auctions/{auction}/toggle-lock', [\App\Http\Controllers\Admin\AuctionController::class, 'toggleLock'])->name('auctions.toggle-lock');
    Route::delete('auctions/{auction}/force-delete', [\App\Http\Controllers\Admin\AuctionController::class, 'forceDelete'])->name('auctions.force-delete');

    // Payment Receipt Moderation
    Route::prefix('payment-receipts')->name('payment-receipts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReceiptReviewController::class, 'index'])->name('index');
        Route::get('{receipt}', [\App\Http\Controllers\Admin\ReceiptReviewController::class, 'show'])->name('show');
        Route::post('{receipt}/approve', [\App\Http\Controllers\Admin\ReceiptReviewController::class, 'approve'])->name('approve');
        Route::post('{receipt}/reject', [\App\Http\Controllers\Admin\ReceiptReviewController::class, 'reject'])->name('reject');
    });

    // Loan Transfer Management
    Route::prefix('loan-transfers')->name('loan-transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TransferController::class, 'index'])->name('index');
        Route::get('{transfer}', [\App\Http\Controllers\Admin\TransferController::class, 'show'])->name('show');
        Route::post('{transfer}/approve', [\App\Http\Controllers\Admin\TransferController::class, 'approve'])->name('approve');
        Route::post('{sale}/complete', [\App\Http\Controllers\Admin\TransferController::class, 'completeSale'])->name('complete-sale');
    });

    // Contracts Management
    Route::get('contracts', [\App\Http\Controllers\Admin\AdminController::class, 'contracts'])->name('contracts.index');

    // Bids Management
    Route::prefix('bids')->name('bids.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'bids'])->name('index');
        Route::post('{bid}/reject', [\App\Http\Controllers\Admin\AdminController::class, 'rejectBid'])->name('reject');
    });

    // Sales Management
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'sales'])->name('index');
        Route::post('{sale}/complete', [\App\Http\Controllers\Admin\AdminController::class, 'completeSale'])->name('complete');
    });
});

// Buyer Panel Routes
Route::prefix('buyer')->name('buyer.')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Buyer\BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('orders', [\App\Http\Controllers\Buyer\BuyerController::class, 'orders'])->name('orders');

    // Auction Flow - All handled in show page
    Route::prefix('auction/{auction}')->name('auction.')->group(function () {
        // Main auction show page - handles all steps
        Route::get('/', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showDetails'])->name('show');

        // Action routes for show page functionality
        Route::post('continue', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'continueToContract'])->name('continue');
        Route::post('bid', [\App\Http\Controllers\Buyer\BidController::class, 'submitBid'])->name('bid.post');
        Route::post('purchase-payment/upload', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'uploadPurchasePayment'])->name('purchase.payment.upload');
        Route::post('payment/receipt', [\App\Http\Controllers\Buyer\ReceiptController::class, 'uploadPaymentReceipt'])->name('payment.receipt');

        // API routes for status checks
        Route::get('bid/status', [\App\Http\Controllers\Buyer\BidController::class, 'getBidStatus'])->name('bid.status');
        Route::get('seller-transfer/status', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'getSellerTransferStatus'])->name('seller-transfer.status');
    });
});

// Seller Panel Routes
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Seller\SellerController::class, 'dashboard'])->name('dashboard');

    // Auction Details
    Route::get('auction/{auction}', [\App\Http\Controllers\Seller\SellerController::class, 'showAuction'])->name('auction.show');

    // Receipt Upload
    Route::post('auction/{auction}/receipt', [\App\Http\Controllers\Seller\SellerController::class, 'uploadReceipt'])->name('receipt.upload');

    // Bid Acceptance
    Route::post('auction/{auction}/bid/accept', [\App\Http\Controllers\Seller\SellerController::class, 'acceptBid'])->name('bid.accept');

    // Loan Transfer
    Route::post('auction/{auction}/loan-transfer', [\App\Http\Controllers\Seller\SellerController::class, 'uploadLoanTransfer'])->name('loan.transfer');

    // IBAN Update
    Route::post('iban/update', [\App\Http\Controllers\Seller\SellerController::class, 'updateIban'])->name('iban.update');

    // Sale Flow - All handled in auction show page
    Route::prefix('sale/{auction}')->name('sale.')->group(function () {
        // Action routes for show page functionality
        Route::post('continue', [\App\Http\Controllers\Seller\SaleFlowController::class, 'continueToContract'])->name('continue');
        Route::post('accept-bid', [\App\Http\Controllers\Seller\SaleFlowController::class, 'acceptBid'])->name('accept-bid');
        Route::post('loan-transfer/receipt', [\App\Http\Controllers\Seller\SaleFlowController::class, 'uploadLoanTransferReceipt'])->name('loan-transfer.receipt');

        // API routes for status checks
        Route::get('buyer-payment/status', [\App\Http\Controllers\Seller\SaleFlowController::class, 'getBuyerPaymentStatus'])->name('buyer-payment.status');
        Route::get('transfer-confirmation/status', [\App\Http\Controllers\Seller\SaleFlowController::class, 'getTransferConfirmationStatus'])->name('transfer-confirmation.status');
    });
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Unified OTP Authentication (New System)
    Route::get('login', [UnifiedOTPController::class, 'showLogin'])->name('unified.otp.login');
    Route::post('unified/otp/request', [UnifiedOTPController::class, 'requestOtp'])->name('unified.otp.request');
    Route::get('unified/otp/verify', [UnifiedOTPController::class, 'showVerify'])->name('unified.otp.verify');
    Route::post('unified/otp/verify', [UnifiedOTPController::class, 'verifyOtp'])->name('unified.otp.verify.post');

    // Legacy Registration Routes (Keep for backward compatibility)
    Route::get('register/buyer', [RegisterController::class, 'showBuyerRegistration'])->name('register.buyer');
    Route::post('register/buyer', [RegisterController::class, 'registerBuyer']);
    Route::get('register/seller', [RegisterController::class, 'showSellerRegistration'])->name('register.seller');
    Route::post('register/seller', [RegisterController::class, 'registerSeller']);

    // Legacy OTP Authentication (Keep for backward compatibility)
    Route::get('legacy/login', [OtpLoginController::class, 'show'])->name('legacy.login');
    Route::post('auth/otp/request', [OTPController::class, 'requestOtp'])->name('otp.request');
    Route::post('auth/otp/verify', [OTPController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('legacy/otp/verify', [OtpLoginController::class, 'showVerify'])->name('otp.verify.form');
});

// Contract OTP Routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::post('contract/otp/send', [OTPController::class, 'sendContractOtp'])->name('contract.otp.send');
    Route::post('contract/otp/verify', [OTPController::class, 'verifyContractOtp'])->name('contract.otp.verify');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->middleware('auth')->group(function () {
    Route::post('initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])->name('initiate');
    Route::get('callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('callback');
    Route::get('success/{payment}', [\App\Http\Controllers\PaymentController::class, 'success'])->name('success');
    Route::get('failed/{payment}', [\App\Http\Controllers\PaymentController::class, 'failed'])->name('failed');

    // Debug route for sandbox status
    Route::get('debug/sandbox', function () {
        $zarinpalService = app(\App\Services\ZarinpalService::class);

        // Use reflection to get actual service values
        $reflection = new \ReflectionClass($zarinpalService);
        $sandboxProperty = $reflection->getProperty('sandbox');
        $sandboxProperty->setAccessible(true);
        $serviceSandbox = $sandboxProperty->getValue($zarinpalService);

        $merchantIdProperty = $reflection->getProperty('merchantId');
        $merchantIdProperty->setAccessible(true);
        $serviceMerchantId = $merchantIdProperty->getValue($zarinpalService);

        $baseUrlProperty = $reflection->getProperty('baseUrl');
        $baseUrlProperty->setAccessible(true);
        $serviceBaseUrl = $baseUrlProperty->getValue($zarinpalService);

        return response()->json([
            'env_sandbox' => env('ZARINPAL_SANDBOX'),
            'config_sandbox' => config('services.zarinpal.sandbox'),
            'service_sandbox' => $serviceSandbox,
            'service_merchant_id' => $serviceMerchantId,
            'service_base_url' => $serviceBaseUrl,
            'config_merchant_id' => config('services.zarinpal.merchant_id'),
            'config_test_merchant_id' => config('services.zarinpal.test_merchant_id'),
            'callback_url' => config('services.zarinpal.callback_url'),
            'app_env' => env('APP_ENV'),
            'debug' => env('APP_DEBUG'),
        ]);
    })->name('debug.sandbox');
});

// Dashboard redirect based on role
Route::get('dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('buyer')) {
        return redirect()->route('buyer.dashboard');
    } elseif ($user->hasRole('seller')) {
        return redirect()->route('seller.dashboard');
    }

    return redirect()->route('unified.otp.login');
})->middleware('auth')->name('dashboard');

// File access routes (protected by policies)
Route::middleware('auth')->group(function () {
    // Receipt images
    Route::get('/receipts/{path}', [App\Http\Controllers\FileController::class, 'showReceipt'])
        ->where('path', '.*')
        ->name('files.receipt');

    // Transfer receipt images
    Route::get('/transfer-receipts/{path}', [App\Http\Controllers\FileController::class, 'showTransferReceipt'])
        ->where('path', '.*')
        ->name('files.transfer-receipt');

    // File downloads
    Route::get('/files/{type}/{path}', [App\Http\Controllers\FileController::class, 'download'])
        ->where('type', 'receipts|transfer-receipts')
        ->where('path', '.*')
        ->name('files.download');

    // File information
    Route::get('/files/{type}/{path}/info', [App\Http\Controllers\FileController::class, 'info'])
        ->where('type', 'receipts|transfer-receipts')
        ->where('path', '.*')
        ->name('files.info');

    // Admin file management
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/files/stats', [App\Http\Controllers\FileController::class, 'stats'])
            ->name('admin.files.stats');
        Route::post('/admin/files/cleanup', [App\Http\Controllers\FileController::class, 'cleanup'])
            ->name('admin.files.cleanup');
    });
});

// Demo route for Persian UI components
Route::get('/demo/persian-ui', function () {
    return view('examples.persian-ui-demo');
})->name('demo.persian-ui');

// Include Breeze authentication routes for admin
require __DIR__.'/auth.php';
