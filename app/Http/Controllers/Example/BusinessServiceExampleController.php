<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\OTPVerifyRequest;
use App\Http\Requests\StoreBidRequest;
use App\Http\Requests\StoreReceiptRequest;
use App\Models\Auction;
use App\Models\User;
use App\Services\AuctionLockService;
use App\Services\BiddingService;
use App\Services\ContractService;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessServiceExampleController extends Controller
{
    protected BiddingService $biddingService;
    protected AuctionLockService $auctionLockService;
    protected ReceiptService $receiptService;
    protected ContractService $contractService;

    public function __construct(
        BiddingService $biddingService,
        AuctionLockService $auctionLockService,
        ReceiptService $receiptService,
        ContractService $contractService
    ) {
        $this->biddingService = $biddingService;
        $this->auctionLockService = $auctionLockService;
        $this->receiptService = $receiptService;
        $this->contractService = $contractService;
    }

    /**
     * Send OTP for contract confirmation
     */
    public function sendContractOTP(OTPRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $auction = Auction::findOrFail($request->route('auction'));

            $result = $this->contractService->sendContractOTP(
                $user,
                $auction,
                \App\Enums\ContractRole::BUYER
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'expires_at' => $result['expires_at'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify contract OTP
     */
    public function verifyContractOTP(OTPVerifyRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $auction = Auction::findOrFail($request->route('auction'));

            $contract = $this->contractService->verifyContractOTP(
                $user,
                $auction,
                \App\Enums\ContractRole::BUYER,
                $request->code
            );

            return response()->json([
                'success' => true,
                'message' => 'قرارداد با موفقیت تأیید شد.',
                'contract' => $contract,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Upload payment receipt
     */
    public function uploadReceipt(StoreReceiptRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $auction = Auction::findOrFail($request->route('auction'));

            // Store the image
            $imagePath = $this->receiptService->storeReceiptImage($request->file('receipt_image'));

            // Create receipt
            $receipt = $this->receiptService->createPendingReceipt(
                $user,
                $auction,
                \App\Enums\PaymentType::from($request->type),
                $request->amount,
                $imagePath
            );

            return response()->json([
                'success' => true,
                'message' => 'رسید پرداخت با موفقیت آپلود شد.',
                'receipt' => $receipt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Place a bid
     */
    public function placeBid(StoreBidRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $auction = Auction::findOrFail($request->route('auction'));

            $bid = $this->biddingService->placeBid($auction, $user, $request->amount);

            return response()->json([
                'success' => true,
                'message' => 'پیشنهاد با موفقیت ثبت شد.',
                'bid' => $bid,
                'is_highest' => $bid->status === \App\Enums\BidStatus::HIGHEST,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Accept a bid (seller action)
     */
    public function acceptBid(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $auction = Auction::findOrFail($request->route('auction'));
            $bid = \App\Models\Bid::findOrFail($request->route('bid'));

            $result = $this->auctionLockService->lockOnAcceptance($auction, $bid);

            return response()->json([
                'success' => true,
                'message' => 'پیشنهاد با موفقیت پذیرفته شد و مزایده قفل شد.',
                'auction' => $result['auction'],
                'bid' => $result['bid'],
                'seller_sale' => $result['seller_sale'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Approve a receipt (admin action)
     */
    public function approveReceipt(Request $request): JsonResponse
    {
        try {
            $admin = auth()->user();
            $receipt = \App\Models\PaymentReceipt::findOrFail($request->route('receipt'));

            $approvedReceipt = $this->receiptService->approveReceipt($receipt, $admin);

            return response()->json([
                'success' => true,
                'message' => 'رسید پرداخت تأیید شد.',
                'receipt' => $approvedReceipt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reject a receipt (admin action)
     */
    public function rejectReceipt(Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $admin = auth()->user();
            $receipt = \App\Models\PaymentReceipt::findOrFail($request->route('receipt'));

            $rejectedReceipt = $this->receiptService->rejectReceipt(
                $receipt,
                $admin,
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'رسید پرداخت رد شد.',
                'receipt' => $rejectedReceipt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get auction status and bidding information
     */
    public function getAuctionStatus(Auction $auction): JsonResponse
    {
        $highestBid = $this->biddingService->getHighestBid($auction);
        $lockInfo = $this->auctionLockService->getLockInfo($auction);
        $receiptStats = $this->receiptService->getReceiptStats();

        return response()->json([
            'auction' => $auction,
            'highest_bid' => $highestBid,
            'lock_info' => $lockInfo,
            'receipt_stats' => $receiptStats,
            'can_bid' => auth()->check() ?
                $this->biddingService->canPlaceBid($auction, auth()->user()) :
                ['can_bid' => false, 'reasons' => ['لطفاً وارد شوید']],
        ]);
    }

    /**
     * Get contract status for user and auction
     */
    public function getContractStatus(Auction $auction): JsonResponse
    {
        $user = auth()->user();
        $contractStatus = $this->contractService->getContractStatus(
            $user,
            $auction,
            \App\Enums\ContractRole::BUYER
        );

        return response()->json([
            'contract_status' => $contractStatus,
            'contract_text' => $this->contractService->getContractText(),
        ]);
    }
}

