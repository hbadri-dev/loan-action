<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerSale;
use App\Models\Auction;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentLinkController extends Controller
{
    /**
     * Display sales awaiting payment link
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        if ($tab === 'completed') {
            // Show sales with payment link
            $sales = SellerSale::with(['seller', 'auction', 'selectedBid.buyer'])
                ->where('status', SaleStatus::AWAITING_BUYER_PAYMENT)
                ->whereNotNull('payment_link')
                ->latest()
                ->paginate(15);
        } else {
            // Show sales without payment link (default)
            $sales = SellerSale::with(['seller', 'auction', 'selectedBid.buyer'])
                ->where('status', SaleStatus::AWAITING_BUYER_PAYMENT)
                ->whereNull('payment_link')
                ->latest()
                ->paginate(15);
        }

        // Get counts for tabs
        $pendingCount = SellerSale::where('status', SaleStatus::AWAITING_BUYER_PAYMENT)
            ->whereNull('payment_link')
            ->count();

        $completedCount = SellerSale::where('status', SaleStatus::AWAITING_BUYER_PAYMENT)
            ->whereNotNull('payment_link')
            ->count();

        return view('admin.payment-links.index', compact('sales', 'pendingCount', 'completedCount'));
    }

    /**
     * Show form to add payment link
     */
    public function create(SellerSale $sale)
    {
        $sale->load(['seller', 'auction', 'selectedBid.buyer']);

        // Check if sale is in correct status
        if ($sale->status !== SaleStatus::AWAITING_BUYER_PAYMENT) {
            return redirect()->route('admin.payment-links.index')
                ->with('error', 'این فروش در مرحله مناسب نیست.');
        }

        return view('admin.payment-links.create', compact('sale'));
    }

    /**
     * Store payment link
     */
    public function store(Request $request, SellerSale $sale)
    {
        $request->validate([
            'payment_link' => 'required|url|max:500',
        ]);

        DB::transaction(function () use ($sale, $request) {
            $sale->update([
                'payment_link' => $request->payment_link,
            ]);

            // Update status to awaiting buyer payment
            $sale->update([
                'status' => SaleStatus::AWAITING_BUYER_PAYMENT,
                'current_step' => 4,
            ]);
        });

        return redirect()->route('admin.payment-links.index')
            ->with('success', 'لینک پرداخت با موفقیت اضافه شد.');
    }

    /**
     * Show edit form
     */
    public function edit(SellerSale $sale)
    {
        $sale->load(['seller', 'auction', 'selectedBid.buyer']);

        return view('admin.payment-links.edit', compact('sale'));
    }

    /**
     * Update payment link
     */
    public function update(Request $request, SellerSale $sale)
    {
        $request->validate([
            'payment_link' => 'required|url|max:500',
        ]);

        $sale->update([
            'payment_link' => $request->payment_link,
        ]);

        return redirect()->route('admin.payment-links.index')
            ->with('success', 'لینک پرداخت به‌روزرسانی شد.');
    }
}
