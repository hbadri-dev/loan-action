# Business Services Documentation

## Overview

The loan auction application uses several business services to handle complex business logic, maintain data integrity through transactions, and provide a clean separation of concerns. All services use database transactions for multi-write operations to ensure data consistency.

## Form Requests

### StoreBidRequest

Validates bid submissions with custom Persian error messages.

**Validation Rules:**

- `amount`: Required integer, must be greater than minimum purchase price and current highest bid
- Custom validation ensures bid amount meets auction requirements

**Usage:**

```php
use App\Http\Requests\StoreBidRequest;

public function store(StoreBidRequest $request)
{
    // Request is automatically validated
    $amount = $request->amount;
}
```

### StoreReceiptRequest

Validates payment receipt uploads.

**Validation Rules:**

- `type`: Required enum (PaymentType)
- `amount`: Required integer, exact amount based on type (3,000,000 for fees)
- `receipt_image`: Required image file (jpg, png, webp), max 5MB

**Usage:**

```php
use App\Http\Requests\StoreReceiptRequest;

public function upload(StoreReceiptRequest $request)
{
    $type = $request->type;
    $amount = $request->amount;
    $image = $request->file('receipt_image');
}
```

### OTPRequest & OTPVerifyRequest

Validate OTP requests and verification.

**Validation Rules:**

- `phone`: Required string, Iranian mobile format (09xxxxxxxxx)
- `code`: Required string, 6-digit numeric code
- `purpose`: Optional enum (login-otp, contract-confirmation)

## Business Services

### BiddingService

Handles all bidding-related business logic with atomic transactions.

#### Methods

**`getHighestBid(Auction $auction): ?Bid`**

- Returns the current highest bid for an auction
- Returns null if no bids exist

**`placeBid(Auction $auction, User $buyer, int $amount): Bid`**

- Places a new bid with atomic transaction
- Validates auction status (active, not locked)
- Validates buyer prerequisites (confirmed contract, approved fee payment)
- Validates bid amount (greater than minimum and current highest)
- Demotes previous highest bid to 'outbid' status
- Sets new bid as 'highest'
- Fires BidPlaced and BidOutbid events

**`canPlaceBid(Auction $auction, User $buyer): array`**

- Checks if buyer can place a bid
- Returns array with 'can_bid' boolean and 'reasons' array

**`getBuyerBids(Auction $auction, User $buyer): Collection`**

- Returns all bids by buyer for specific auction

#### Usage Example

```php
use App\Services\BiddingService;

class BidController extends Controller
{
    public function store(StoreBidRequest $request, BiddingService $biddingService)
    {
        $auction = Auction::findOrFail($request->route('auction'));
        $buyer = auth()->user();

        try {
            $bid = $biddingService->placeBid($auction, $buyer, $request->amount);

            return response()->json([
                'success' => true,
                'bid' => $bid,
                'is_highest' => $bid->status === BidStatus::HIGHEST,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
```

### AuctionLockService

Manages auction locking when bids are accepted.

#### Methods

**`lockOnAcceptance(Auction $auction, Bid $bid): array`**

- Locks auction when seller accepts a bid
- Updates auction status to 'locked'
- Updates bid status to 'accepted'
- Creates/updates SellerSale record
- Creates LoanTransfer record
- Fires BidAccepted event
- Returns array with all updated models

**`unlock(Auction $auction, string $reason = null): Auction`**

- Admin function to unlock an auction
- Resets auction status to 'active'
- Resets accepted bid to 'highest'
- Updates seller sale status

**`forceLock(Auction $auction, string $reason = null): Auction`**

- Admin function to force lock an auction
- Emergency function for administrative purposes

**`isLocked(Auction $auction): bool`**

- Checks if auction is currently locked

**`getLockInfo(Auction $auction): array`**

- Returns comprehensive lock information

#### Usage Example

```php
use App\Services\AuctionLockService;

class SellerController extends Controller
{
    public function acceptBid(Request $request, AuctionLockService $auctionLockService)
    {
        $auction = Auction::findOrFail($request->route('auction'));
        $bid = Bid::findOrFail($request->route('bid'));

        try {
            $result = $auctionLockService->lockOnAcceptance($auction, $bid);

            return response()->json([
                'success' => true,
                'message' => 'پیشنهاد پذیرفته شد و مزایده قفل شد.',
                'auction' => $result['auction'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
```

### ReceiptService

Manages payment receipt creation, approval, and rejection.

#### Methods

**`createPendingReceipt(User $user, Auction $auction, PaymentType $type, int $amount, string $imagePath): PaymentReceipt`**

- Creates or updates pending payment receipt
- Validates amount based on payment type
- Handles re-upload of rejected receipts
- Returns PaymentReceipt model

**`storeReceiptImage(UploadedFile $file): string`**

- Stores uploaded receipt image
- Returns file path for database storage
- Handles file storage errors

**`approveReceipt(PaymentReceipt $receipt, User $reviewer): PaymentReceipt`**

- Approves a pending receipt
- Updates status to 'approved'
- Sets reviewer information
- Fires ReceiptApproved event

**`rejectReceipt(PaymentReceipt $receipt, User $reviewer, string $reason): PaymentReceipt`**

- Rejects a pending receipt
- Updates status to 'rejected'
- Sets rejection reason
- Fires ReceiptRejected event

**`hasApprovedReceipt(User $user, Auction $auction, PaymentType $type): bool`**

- Checks if user has approved receipt for specific type

**`getReceiptStats(): array`**

- Returns receipt statistics for admin dashboard

#### Usage Example

```php
use App\Services\ReceiptService;

class ReceiptController extends Controller
{
    public function upload(StoreReceiptRequest $request, ReceiptService $receiptService)
    {
        $user = auth()->user();
        $auction = Auction::findOrFail($request->route('auction'));

        try {
            $imagePath = $receiptService->storeReceiptImage($request->file('receipt_image'));

            $receipt = $receiptService->createPendingReceipt(
                $user,
                $auction,
                PaymentType::from($request->type),
                $request->amount,
                $imagePath
            );

            return response()->json([
                'success' => true,
                'receipt' => $receipt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
```

### ContractService

Handles contract confirmation through OTP verification.

#### Methods

**`sendContractOTP(User $user, Auction $auction, ContractRole $role): array`**

- Sends OTP for contract confirmation
- Validates user prerequisites
- Prevents duplicate OTP requests
- Returns OTP information

**`verifyContractOTP(User $user, Auction $auction, ContractRole $role, string $code): ContractAgreement`**

- Verifies OTP and creates contract agreement
- Marks OTP as used
- Creates or updates ContractAgreement
- Returns ContractAgreement model

**`hasConfirmedContract(User $user, Auction $auction, ContractRole $role): bool`**

- Checks if user has confirmed contract

**`getContractStatus(User $user, Auction $auction, ContractRole $role): array`**

- Returns detailed contract status information

**`getContractText(): string`**

- Returns contract text from configuration

**`cleanupExpiredOTPs(): int`**

- Cleans up expired OTP codes
- Returns number of deleted codes

#### Usage Example

```php
use App\Services\ContractService;

class ContractController extends Controller
{
    public function sendOTP(Request $request, ContractService $contractService)
    {
        $user = auth()->user();
        $auction = Auction::findOrFail($request->route('auction'));

        try {
            $result = $contractService->sendContractOTP(
                $user,
                $auction,
                ContractRole::BUYER
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
}
```

## Events and Listeners

### Events

**BidPlaced**

- Fired when a new bid is placed
- Broadcasts to auction channel
- Includes bid and auction information

**BidOutbid**

- Fired when a bid is outbid by a higher bid
- Broadcasts to user and auction channels
- Includes both bid information

**BidAccepted**

- Fired when seller accepts a bid
- Broadcasts to buyer, seller, and auction channels
- Includes comprehensive transaction information

**ReceiptApproved**

- Fired when admin approves a receipt
- Broadcasts to user and auction channels
- Includes receipt and reviewer information

**ReceiptRejected**

- Fired when admin rejects a receipt
- Broadcasts to user and auction channels
- Includes rejection reason

### Listeners

All listeners implement `ShouldQueue` for asynchronous processing:

- `SendBidPlacedNotification`
- `SendBidOutbidNotification`
- `SendBidAcceptedNotification`
- `SendReceiptApprovedNotification`
- `SendReceiptRejectedNotification`

## Database Transactions

All services use database transactions for multi-write operations:

```php
return DB::transaction(function () use ($params) {
    // Multiple database operations
    $model1 = Model1::create($data1);
    $model2 = Model2::create($data2);

    // If any operation fails, all are rolled back
    return $result;
});
```

### Transaction Benefits

1. **Atomicity**: All operations succeed or fail together
2. **Consistency**: Database remains in valid state
3. **Isolation**: Concurrent operations don't interfere
4. **Durability**: Committed changes persist

## Error Handling

All services throw descriptive exceptions with Persian messages:

```php
throw new \Exception('مزایده در حال حاضر فعال نیست.');
```

Services log all operations for debugging and monitoring:

```php
Log::info('Bid placed successfully', [
    'auction_id' => $auction->id,
    'buyer_id' => $buyer->id,
    'amount' => $amount,
]);
```

## Service Registration

Services are registered in `BusinessServiceProvider`:

```php
$this->app->singleton(BiddingService::class, function ($app) {
    return new BiddingService();
});
```

Available aliases:

- `service.bidding` → BiddingService
- `service.auction_lock` → AuctionLockService
- `service.receipt` → ReceiptService
- `service.contract` → ContractService

## Testing

Services can be tested by mocking dependencies:

```php
public function test_place_bid()
{
    $biddingService = new BiddingService();
    $auction = Auction::factory()->create();
    $buyer = User::factory()->create();

    $bid = $biddingService->placeBid($auction, $buyer, 1000000);

    $this->assertEquals(1000000, $bid->amount);
    $this->assertEquals(BidStatus::HIGHEST, $bid->status);
}
```

## Best Practices

1. **Always use transactions** for multi-write operations
2. **Validate prerequisites** before performing operations
3. **Throw descriptive exceptions** with Persian messages
4. **Log all operations** for debugging and monitoring
5. **Fire events** for important state changes
6. **Use dependency injection** for service dependencies
7. **Handle file operations** with proper error handling
8. **Clean up expired data** regularly

