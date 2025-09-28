<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\SellerSale;
use App\Services\BuyerProgressService;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptReviewController extends Controller
{
    protected BuyerProgressService $progressService;

    public function __construct(BuyerProgressService $progressService)
    {
        $this->progressService = $progressService;
    }
    /**
     * Display a listing of payment receipts
     */
    public function index(Request $request)
    {
        $query = PaymentReceipt::with(['user', 'auction', 'reviewer']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $receipts = $query->latest()->paginate(15);

        return view('admin.payment-receipts.index', compact('receipts'));
    }

    /**
     * Display the specified payment receipt
     */
    public function show(PaymentReceipt $receipt)
    {
        $receipt->load(['user', 'auction', 'reviewer']);
        return view('admin.payment-receipts.show', compact('receipt'));
    }

    /**
     * Approve payment receipt
     */
    public function approve(Request $request, PaymentReceipt $receipt)
    {
        $receipt->update([
            'status' => PaymentStatus::APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update related sale status if it's a buyer fee payment
        if ($receipt->type === PaymentType::BUYER_FEE) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)->first();
            if ($sellerSale) {
                $sellerSale->update(['status' => SaleStatus::FEE_APPROVED]);
            }

            // Update buyer progress to step 4 (bid submission)
            $this->progressService->updateProgress($receipt->auction, $receipt->user, 'bid', 4);
        }

        // Update seller sale status if it's a seller fee payment
        if ($receipt->type === PaymentType::SELLER_FEE) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)
                ->where('seller_id', $receipt->user_id)
                ->first();
            if ($sellerSale) {
                $sellerSale->update([
                    'status' => SaleStatus::FEE_APPROVED,
                    'current_step' => 3, // Move to step 3 (bid acceptance)
                ]);
            }
        }

        // Update related sale status if it's a buyer purchase payment
        if ($receipt->type === PaymentType::BUYER_PURCHASE_AMOUNT) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)->first();
            if ($sellerSale) {
                $sellerSale->update([
                    'status' => SaleStatus::BUYER_PAYMENT_APPROVED,
                    'current_step' => 5,
                ]);
            }

            // Update buyer progress to step 7 (awaiting seller transfer)
            $buyerProgress = \App\Models\BuyerProgress::where('auction_id', $receipt->auction_id)
                ->where('user_id', $receipt->user_id)
                ->first();
            if ($buyerProgress) {
                $this->progressService->updateProgress(
                    \App\Models\Auction::find($receipt->auction_id),
                    $receipt->user,
                    'awaiting-seller-transfer',
                    7
                );
            }
        }

        // Update related sale status if it's a loan transfer receipt
        if ($receipt->type === PaymentType::LOAN_TRANSFER) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)->first();
            if ($sellerSale) {
                $sellerSale->update([
                    'status' => SaleStatus::TRANSFER_CONFIRMED,
                    'current_step' => 7,
                ]);
            }

            // Update buyer progress to step 9 (complete)
            $buyerProgress = \App\Models\BuyerProgress::where('auction_id', $receipt->auction_id)->first();
            if ($buyerProgress) {
                $this->progressService->updateProgress(
                    \App\Models\Auction::find($receipt->auction_id),
                    $buyerProgress->user,
                    'complete',
                    9
                );
            }
        }

        // Notify user of approval
        $receipt->user->notify(new \App\Notifications\PaymentReceiptApproved($receipt));

        return redirect()->back()->with('success', 'رسید پرداخت تأیید شد.');
    }

    /**
     * Reject payment receipt
     */
    public function reject(Request $request, PaymentReceipt $receipt)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $receipt->update([
            'status' => PaymentStatus::REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        // Notify user of rejection
        $receipt->user->notify(new \App\Notifications\PaymentReceiptRejected($receipt));

        return redirect()->back()->with('success', 'رسید پرداخت رد شد.');
    }
}
