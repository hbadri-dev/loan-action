<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Models\Payment;
use App\Models\LoanTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Auction::with([
            'creator',
            'sellerSales',
            'bids' => function ($query) {
                $query->where('status', \App\Enums\BidStatus::ACCEPTED);
            },
            'payments',
            'loanTransfers'
        ]);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by completion status
        if ($request->has('completion_status')) {
            switch ($request->completion_status) {
                case 'completed':
                    $query->whereHas('sellerSales', function ($q) {
                        $q->where('status', \App\Enums\SaleStatus::COMPLETED);
                    });
                    break;
                case 'in_progress':
                    $query->whereHas('sellerSales', function ($q) {
                        $q->where('status', '!=', \App\Enums\SaleStatus::COMPLETED)
                          ->where('status', '!=', \App\Enums\SaleStatus::CANCELLED);
                    });
                    break;
                case 'cancelled':
                    $query->whereHas('sellerSales', function ($q) {
                        $q->where('status', \App\Enums\SaleStatus::CANCELLED);
                    });
                    break;
            }
        }

        $auctions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total_auctions' => Auction::count(),
            'completed_loans' => Auction::whereHas('sellerSales', function ($q) {
                $q->where('status', \App\Enums\SaleStatus::COMPLETED);
            })->count(),
            'in_progress_loans' => Auction::whereHas('sellerSales', function ($q) {
                $q->where('status', '!=', \App\Enums\SaleStatus::COMPLETED)
                  ->where('status', '!=', \App\Enums\SaleStatus::CANCELLED);
            })->count(),
            'total_volume' => Auction::whereHas('sellerSales', function ($q) {
                $q->where('status', \App\Enums\SaleStatus::COMPLETED);
            })->sum('principal_amount'),
        ];

        return view('admin.loan-history.index', compact('auctions', 'stats'));
    }

    public function show(Auction $auction)
    {
        // Get seller sale for this auction
        $sellerSale = $auction->sellerSales()->first();

        // Get accepted bid
        $acceptedBid = $auction->bids()->where('status', \App\Enums\BidStatus::ACCEPTED)->first();

        // Get all payments for this auction
        $payments = $auction->payments()->get();

        // Get loan transfer if exists
        $loanTransfer = $auction->loanTransfers()->first();

        // Get buyer progress
        $buyerProgress = $auction->buyerProgress()->get();

        return view('admin.loan-history.show', compact('auction', 'sellerSale', 'acceptedBid', 'payments', 'loanTransfer', 'buyerProgress'));
    }

    public function export(Request $request)
    {
        $query = Auction::with([
            'creator',
            'sellerSales',
            'bids' => function ($query) {
                $query->where('status', \App\Enums\BidStatus::ACCEPTED);
            },
            'payments',
            'loanTransfers'
        ]);

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auctions = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'loan_history_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($auctions) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'شناسه مزایده',
                'عنوان',
                'مبلغ وام',
                'نرخ بهره',
                'مدت (ماه)',
                'فروشنده',
                'خریدار',
                'وضعیت مزایده',
                'وضعیت فروش',
                'تاریخ ایجاد',
                'تاریخ تکمیل'
            ]);

            foreach ($auctions as $auction) {
                $acceptedBid = $auction->bids->first();
                $sellerSale = $auction->sellerSales->first();

                fputcsv($file, [
                    $auction->id,
                    $auction->title,
                    number_format($auction->principal_amount),
                    $auction->interest_rate_percent . '%',
                    $auction->term_months,
                    $auction->creator->name ?? 'نامشخص',
                    $acceptedBid ? $acceptedBid->buyer->name : 'نامشخص',
                    $auction->status->label(),
                    $sellerSale ? $sellerSale->status->label() : 'نامشخص',
                    $auction->created_at->format('Y-m-d H:i:s'),
                    $auction->completed_at ? $auction->completed_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


