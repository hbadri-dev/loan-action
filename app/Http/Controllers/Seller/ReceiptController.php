<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\PaymentReceipt;
use App\Models\SellerSale;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Show payment form (Step 2)
     */
    public function showPayment(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale || $sellerSale->current_step < 2) {
            return redirect()->route('seller.sale.details', $auction);
        }

        // Skip contract check - go directly to payment

        // Get or create payment receipt
        $paymentReceipt = PaymentReceipt::firstOrCreate(
            [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'type' => PaymentType::SELLER_FEE,
            ],
            [
                'amount' => 200000, // 200,000 Toman
                'status' => PaymentStatus::PENDING_REVIEW,
            ]
        );

        return view('seller.sale.payment', compact('auction', 'paymentReceipt', 'sellerSale'));
    }

    /**
     * Upload payment receipt (Step 2)
     */
    public function uploadPaymentReceipt(Request $request, Auction $auction)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::SELLER_FEE)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('seller.sale.details', $auction);
        }

        // Store the uploaded file
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        // Update payment receipt - allow re-upload if rejected
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
            'reject_reason' => null, // Clear previous reject reason
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        // Update seller sale step
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        $sellerSale->update([
            'current_step' => 3, // Go to step 3 (bid acceptance)
            'status' => SaleStatus::FEE_APPROVED
        ]);

        return redirect()->route('seller.sale.payment', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
    }
}
