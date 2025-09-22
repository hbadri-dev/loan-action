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

    // Auction Flow (Steps 1-7)
    Route::prefix('auction/{auction}')->name('auction.')->group(function () {
        // Join auction - redirect to appropriate step based on progress
        Route::get('join', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'joinAuction'])->name('join');

        // Step 1: Auction Details
        Route::get('/', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showDetails'])->name('show');
        Route::get('details', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showDetails'])->name('details');
        Route::post('continue', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'continueToContract'])->name('continue');

        // Step 2: Contract Confirmation
        Route::get('contract', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showContract'])->name('contract');
        Route::post('contract/otp', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'sendContractOtp'])->name('contract.otp');
        Route::post('contract/verify-otp', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'verifyContractOtp'])->name('contract.verify-otp');
        Route::get('contract/verify', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showContractVerification'])->name('verify-contract');
        Route::post('contract/verify', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'verifyContract'])->name('verify-contract.post');

        // Step 3: Payment Receipt Upload
        Route::get('payment', [\App\Http\Controllers\Buyer\ReceiptController::class, 'showPayment'])->name('payment');
        Route::post('payment/receipt', [\App\Http\Controllers\Buyer\ReceiptController::class, 'uploadPaymentReceipt'])->name('payment.receipt');

        // Step 4: Bid Submission
        Route::get('bid', [\App\Http\Controllers\Buyer\BidController::class, 'showBidForm'])->name('bid');
        Route::post('bid', [\App\Http\Controllers\Buyer\BidController::class, 'submitBid'])->name('bid.post');

        // Step 5: Waiting for Seller
        Route::get('waiting-seller', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showWaitingSeller'])->name('waiting-seller');
        Route::get('bid/status', [\App\Http\Controllers\Buyer\BidController::class, 'getBidStatus'])->name('bid.status');

        // Step 6: Purchase Payment
        Route::get('purchase-payment', [\App\Http\Controllers\Buyer\ReceiptController::class, 'showPurchasePayment'])->name('purchase-payment');
        Route::post('purchase-payment/receipt', [\App\Http\Controllers\Buyer\ReceiptController::class, 'uploadPurchaseReceipt'])->name('purchase-payment.receipt');

        // Unified purchase payment upload for show page
        Route::post('purchase-payment/upload', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'uploadPurchasePayment'])->name('purchase.payment.upload');

        // Step 7: Awaiting Seller Transfer
        Route::get('awaiting-seller-transfer', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showAwaitingSellerTransfer'])->name('awaiting-seller-transfer');
        Route::get('seller-transfer/status', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'getSellerTransferStatus'])->name('seller-transfer.status');

        // Step 8: Confirm Transfer
        Route::get('confirm-transfer', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showConfirmTransfer'])->name('confirm-transfer');
        Route::post('loan-transfer/confirm', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'confirmLoanTransfer'])->name('loan-transfer.confirm');

        // Final: Completion
        Route::get('complete', [\App\Http\Controllers\Buyer\AuctionFlowController::class, 'showComplete'])->name('complete');
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

    // Sale Flow (Steps 1-8)
    Route::prefix('sale/{auction}')->name('sale.')->group(function () {
        // Start Sale
        Route::post('start', [\App\Http\Controllers\Seller\SaleFlowController::class, 'startSale'])->name('start');

        // Step 1: Sale Details
        Route::get('details', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showSaleDetails'])->name('details');
        Route::post('continue', [\App\Http\Controllers\Seller\SaleFlowController::class, 'continueToContract'])->name('continue');

        // Step 2: Contract Confirmation
        Route::get('contract', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showContract'])->name('contract');
        Route::post('contract/otp', [\App\Http\Controllers\Seller\SaleFlowController::class, 'sendContractOtp'])->name('contract.otp');
        Route::get('contract/verify', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showContractVerification'])->name('verify-contract');
        Route::post('contract/verify', [\App\Http\Controllers\Seller\SaleFlowController::class, 'verifyContract'])->name('verify-contract.post');

        // OTP routes for seller show page
        Route::post('contract/otp-send', [\App\Http\Controllers\Seller\SellerController::class, 'sendContractOtp'])->name('contract.otp-send');
        Route::post('contract/verify-otp', [\App\Http\Controllers\Seller\SellerController::class, 'verifyContractOtp'])->name('contract.verify-otp');

        // Step 3: Payment Receipt Upload
        Route::get('payment', [\App\Http\Controllers\Seller\ReceiptController::class, 'showPayment'])->name('payment');
        Route::post('payment/receipt', [\App\Http\Controllers\Seller\ReceiptController::class, 'uploadPaymentReceipt'])->name('seller.payment.receipt');

        // Step 4: Bid Acceptance
        Route::get('bid-acceptance', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showBidAcceptance'])->name('bid-acceptance');
        Route::post('accept-bid', [\App\Http\Controllers\Seller\SaleFlowController::class, 'acceptBid'])->name('accept-bid');

        // Step 5: Awaiting Buyer Payment
        Route::get('awaiting-buyer-payment', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showAwaitingBuyerPayment'])->name('awaiting-buyer-payment');
        Route::get('buyer-payment/status', [\App\Http\Controllers\Seller\SaleFlowController::class, 'getBuyerPaymentStatus'])->name('buyer-payment.status');

        // Step 6: Loan Transfer
        Route::get('loan-transfer', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showLoanTransfer'])->name('loan-transfer');
        Route::post('loan-transfer/receipt', [\App\Http\Controllers\Seller\SaleFlowController::class, 'uploadLoanTransferReceipt'])->name('loan-transfer.receipt');

        // Step 7: Awaiting Transfer Confirmation
        Route::get('awaiting-transfer-confirmation', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showAwaitingTransferConfirmation'])->name('awaiting-transfer-confirmation');
        Route::get('transfer-confirmation/status', [\App\Http\Controllers\Seller\SaleFlowController::class, 'getTransferConfirmationStatus'])->name('transfer-confirmation.status');

        // Step 8: Sale Completion
        Route::get('completion', [\App\Http\Controllers\Seller\SaleFlowController::class, 'showSaleCompletion'])->name('completion');
        Route::post('complete', [\App\Http\Controllers\Seller\SaleFlowController::class, 'completeSale'])->name('complete');
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

    return redirect()->route('login');
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
