<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\PaymentReceipt;
use App\Models\Bid;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Services\BuyerProgressService;
use App\Services\FileUploadService;
use App\Services\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    protected BuyerProgressService $progressService;
    protected FileUploadService $fileUploadService;
    protected AdminNotifier $adminNotifier;

    public function __construct(BuyerProgressService $progressService, FileUploadService $fileUploadService, AdminNotifier $adminNotifier)
    {
        $this->progressService = $progressService;
        $this->fileUploadService = $fileUploadService;
        $this->adminNotifier = $adminNotifier;
    }
    /**
     * Show payment form (Step 3) - Now uses Zarinpal
     */
    public function showPayment(Auction $auction)
    {
        $user = Auth::user();

        // Check if contract is confirmed
        $contract = \App\Models\ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'buyer')
            ->where('status', 'confirmed')
            ->first();

        if (!$contract) {
            return redirect()->route('buyer.auction.contract', $auction);
        }

        // Check if payment already exists and is completed
        $payment = \App\Models\Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_FEE)
            ->where('status', \App\Enums\PaymentStatus::COMPLETED)
            ->first();

        if ($payment) {
            // Payment already completed, redirect to next step
            return redirect()->route('buyer.auction.bid', $auction);
        }

        return view('buyer.auction.payment', compact('auction'));
    }

    /**
     * Upload payment receipt (Step 3)
     */
    public function uploadPaymentReceipt(Request $request, Auction $auction)
    {
        \Log::info('Payment receipt upload started', [
            'user_id' => Auth::id(),
            'auction_id' => $auction->id,
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'receipt_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            ]);

            \Log::info('Validation passed for payment receipt upload');

            $user = Auth::user();

            $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('type', PaymentType::BUYER_FEE)
                ->first();

            \Log::info('Payment receipt found or will be created', [
                'payment_receipt_exists' => $paymentReceipt ? true : false,
                'payment_receipt_id' => $paymentReceipt ? $paymentReceipt->id : null
            ]);

            if (!$paymentReceipt) {
                // Create payment receipt if it doesn't exist
                $paymentReceipt = PaymentReceipt::create([
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'type' => PaymentType::BUYER_FEE,
                    'amount' => 3000000, // 3,000,000 Toman
                    'status' => PaymentStatus::PENDING_REVIEW,
                ]);
                \Log::info('Payment receipt created', ['payment_receipt_id' => $paymentReceipt->id]);
            }

        // Store the uploaded file using FileUploadService
        \Log::info('Starting file upload via FileUploadService');
        $imagePath = $this->fileUploadService->storeReceiptImage($request->file('receipt_image'), $user->id);
        \Log::info('File uploaded successfully', ['image_path' => $imagePath]);

        // Update payment receipt - allow re-upload if rejected
        \Log::info('Updating payment receipt with image path');
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
            'reject_reason' => null, // Clear previous reject reason
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        \Log::info('Payment receipt updated successfully', [
            'payment_receipt_id' => $paymentReceipt->id,
            'image_path' => $imagePath
        ]);

        // Notify admin about payment receipt upload
        $this->adminNotifier->notifyBuyerAction('payment_receipt_uploaded', $user, [
            'auction_title' => $auction->title
        ]);

        \Log::info('Redirecting to buyer.auction.show');
        return redirect()->route('buyer.auction.show', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
        } catch (\Exception $e) {
            \Log::error('Payment receipt upload error', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'auction_id' => $auction->id,
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'خطا در آپلود رسید: ' . $e->getMessage());
        }
    }

    /**
     * Show purchase payment form (Step 6) - Now uses Zarinpal
     */
    public function showPurchasePayment(Auction $auction)
    {
        $user = Auth::user();

        // Get user's accepted bid
        $userBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$userBid) {
            return redirect()->route('buyer.dashboard');
        }

        // Check if payment already exists and is completed
        $payment = \App\Models\Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->where('status', \App\Enums\PaymentStatus::COMPLETED)
            ->first();

        if ($payment) {
            // Payment already completed, redirect to next step
            return redirect()->route('buyer.auction.awaiting-seller-transfer', $auction);
        }

        return view('buyer.auction.purchase-payment', compact('auction', 'userBid'));
    }

    /**
     * Upload purchase payment receipt (Step 6)
     */
    public function uploadPurchaseReceipt(Request $request, Auction $auction)
    {
        $request->validate([
            'receipt_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            'national_id' => 'required|string|size:10',
        ]);

        $user = Auth::user();

        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Store the uploaded file using FileUploadService
        $imagePath = $this->fileUploadService->storeReceiptImage($request->file('receipt_image'), $user->id);

        // Update user's national_id
        $user->update(['national_id' => $request->national_id]);

        // Update payment receipt
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
            'reject_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        // Store national ID in loan transfer record
        $loanTransfer = \App\Models\LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if ($loanTransfer) {
            $loanTransfer->update([
                'national_id_of_buyer' => $request->national_id,
            ]);
        }

        // Update progress to awaiting seller transfer step
        $this->progressService->updateProgress($auction, $user, 'awaiting-seller-transfer', 6);

        return redirect()->route('buyer.auction.awaiting-seller-transfer', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
    }
}
