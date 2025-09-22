# Auction Lock Enforcement Documentation

## Overview

The auction lock enforcement system ensures data integrity and prevents race conditions when managing auction states. It includes server-side validation, database-level safety, and UI feedback to provide a secure and user-friendly experience.

## Database Schema

### New Column: `auctions.is_locked`

```sql
ALTER TABLE auctions ADD COLUMN is_locked BOOLEAN DEFAULT FALSE;
CREATE INDEX idx_auctions_status_locked ON auctions(status, is_locked);
```

**Purpose:**

- Quick database-level checks for auction lock status
- Performance optimization for queries filtering locked auctions
- Redundant safety mechanism alongside status-based checks

**Synchronization:**

- Automatically synced with `status` column via model mutator
- `status = 'locked'` → `is_locked = true`
- `status != 'locked'` → `is_locked = false`

## Server-Side Enforcement

### BidController@store

**Validation Logic:**

```php
// Server-side enforcement: Check if auction is locked
if ($auction->isLocked()) {
    abort(422, 'مزایده قفل است.');
}
```

**Error Response:**

- HTTP Status: 422 Unprocessable Entity
- Message: "مزایده قفل است."
- Persian error message for user feedback

### BiddingService Validation

**Auction Status Checks:**

```php
private function validateAuctionForBidding(Auction $auction): void
{
    if ($auction->status !== AuctionStatus::ACTIVE) {
        throw new \Exception('مزایده در حال حاضر فعال نیست.');
    }

    if ($auction->isLocked()) {
        throw new \Exception('مزایده قفل است.');
    }
}
```

**Validation Points:**

1. Auction must be in `ACTIVE` status
2. Auction must not be locked (`is_locked = false`)
3. Buyer must have confirmed contract
4. Buyer must have approved fee payment
5. Bid amount must exceed minimum and current highest

## Bid Status Management

### When Seller Accepts Bid

**AuctionLockService::lockOnAcceptance()** performs atomic operations:

```php
return DB::transaction(function () use ($auction, $bid) {
    // 1. Lock auction
    $auction->update([
        'status' => AuctionStatus::LOCKED,
        'locked_at' => now(),
    ]);

    // 2. Accept selected bid
    $bid->update(['status' => BidStatus::ACCEPTED]);

    // 3. Reject all other bids
    Bid::where('auction_id', $auction->id)
        ->where('id', '!=', $bid->id)
        ->update(['status' => BidStatus::REJECTED]);

    // 4. Create seller sale and loan transfer records
    // ... additional business logic
});
```

**Result:**

- Auction status: `LOCKED`
- Auction is_locked: `true`
- Selected bid status: `ACCEPTED`
- All other bids status: `REJECTED`
- New SellerSale and LoanTransfer records created

## UI Components

### Auction Locked Banner

**Component:** `auction-locked-banner.blade.php`

```blade
@if($auction->isLocked())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-red-800">مزایده قفل شده</h3>
        <p class="text-sm text-red-700">
            این مزایده قفل شده است و امکان ثبت پیشنهاد جدید وجود ندارد.
        </p>
    </div>
@endif
```

**Features:**

- Conditional display based on lock status
- Persian messaging
- RTL layout support
- Lock timestamp display

### Bid Form Component

**Component:** `bid-form.blade.php`

**Locked State:**

```blade
@if($auction->isLocked())
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <p class="text-gray-600">
            این مزایده قفل شده است و امکان ثبت پیشنهاد جدید وجود ندارد.
        </p>
    </div>
@else
    <!-- Bid form with input fields -->
@endif
```

**Active State:**

- Input field for bid amount
- Current highest bid display
- Minimum purchase price display
- Form validation and submission

### Status Indicator

**Component:** `auction-status-indicator.blade.php`

**Status Types:**

- `ACTIVE`: Green badge with checkmark
- `LOCKED`: Red badge with lock icon
- `COMPLETED`: Blue badge with completion icon
- `PAUSED`: Yellow badge with pause icon

## Model Enhancements

### Auction Model

**New Properties:**

```php
protected $fillable = [
    // ... existing fields
    'is_locked',
];

protected function casts(): array
{
    return [
        // ... existing casts
        'is_locked' => 'boolean',
    ];
}
```

**Status Mutator:**

```php
public function setStatusAttribute($value): void
{
    $this->attributes['status'] = $value;

    // Auto-sync is_locked with status
    if ($value === AuctionStatus::LOCKED) {
        $this->attributes['is_locked'] = true;
    } else {
        $this->attributes['is_locked'] = false;
    }
}
```

**Helper Method:**

```php
public function isLocked(): bool
{
    return $this->is_locked || $this->status === AuctionStatus::LOCKED;
}
```

## Database Queries

### Performance Optimized Queries

**Active Auctions:**

```php
// Fast query using index
$activeAuctions = Auction::where('status', AuctionStatus::ACTIVE)
    ->where('is_locked', false)
    ->get();
```

**Locked Auctions:**

```php
// Fast query using index
$lockedAuctions = Auction::where('is_locked', true)
    ->get();
```

**Bid Filtering:**

```php
// Prevent bidding on locked auctions
$availableAuctions = Auction::where('status', AuctionStatus::ACTIVE)
    ->where('is_locked', false)
    ->with('bids')
    ->get();
```

## Error Handling

### Validation Errors

**Bid Submission:**

- `422`: Auction is locked
- `403`: User not authorized
- `400`: Validation failed (amount, prerequisites)

**Error Messages (Persian):**

- "مزایده قفل است." - Auction is locked
- "مزایده در حال حاضر فعال نیست." - Auction not active
- "شما مجاز به ثبت پیشنهاد در این مزایده نیستید." - Not authorized

### Exception Handling

**Service Layer:**

```php
try {
    $bid = $this->biddingService->placeBid($auction, $user, $amount);
    return response()->json(['success' => true, 'bid' => $bid]);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
```

## Testing

### Test Coverage

**AuctionLockEnforcementTest** covers:

1. **Bid Prevention:**

   - Cannot bid on locked auction
   - Cannot bid on inactive auction
   - Can bid on active unlocked auction

2. **Status Management:**

   - All bids rejected when accepting highest
   - Auction locked after acceptance
   - Status and is_locked synchronization

3. **Admin Functions:**
   - Unlock auction functionality
   - Force lock auction functionality
   - Lock information retrieval

### Test Examples

```php
/** @test */
public function it_prevents_bidding_on_locked_auction()
{
    $this->auction->update(['is_locked' => true]);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('مزایده قفل است.');

    $this->biddingService->placeBid($this->auction, $this->buyer, 1000000);
}
```

## Migration and Deployment

### Migration Steps

1. **Add Column:**

```bash
php artisan migrate
```

2. **Update Existing Data:**

```bash
php artisan db:seed --class=UpdateAuctionLockStatusSeeder
```

3. **Verify Sync:**

```php
// Ensure all LOCKED auctions have is_locked = true
Auction::where('status', AuctionStatus::LOCKED)
    ->where('is_locked', false)
    ->update(['is_locked' => true]);
```

### Rollback Plan

**Migration Rollback:**

```php
public function down(): void
{
    Schema::table('auctions', function (Blueprint $table) {
        $table->dropIndex(['status', 'is_locked']);
        $table->dropColumn('is_locked');
    });
}
```

## Performance Considerations

### Database Indexes

**Composite Index:**

```sql
CREATE INDEX idx_auctions_status_locked ON auctions(status, is_locked);
```

**Benefits:**

- Fast filtering of active unlocked auctions
- Efficient bid validation queries
- Optimized dashboard queries

### Query Optimization

**Eager Loading:**

```php
$auctions = Auction::with(['bids', 'user'])
    ->where('status', AuctionStatus::ACTIVE)
    ->where('is_locked', false)
    ->get();
```

**Caching:**

```php
// Cache auction lock status for frequently accessed auctions
Cache::remember("auction.{$auctionId}.locked", 300, function() use ($auction) {
    return $auction->isLocked();
});
```

## Security Considerations

### Race Condition Prevention

**Database Locks:**

```php
DB::transaction(function () use ($auction, $bid) {
    $auction = Auction::lockForUpdate()->findOrFail($auction->id);
    // Safe to check and update status
});
```

### Authorization

**Policy Checks:**

```php
if (!$user->can('create', [Bid::class, $auction])) {
    abort(403, 'شما مجاز به ثبت پیشنهاد در این مزایده نیستید.');
}
```

## Monitoring and Logging

### Audit Trail

**Lock Events:**

```php
Log::info('Auction locked on bid acceptance', [
    'auction_id' => $auction->id,
    'bid_id' => $bid->id,
    'buyer_id' => $bid->user_id,
    'locked_at' => now(),
]);
```

**Unlock Events:**

```php
Log::info('Auction unlocked', [
    'auction_id' => $auction->id,
    'reason' => $reason,
    'unlocked_at' => now(),
]);
```

## Best Practices

1. **Always use transactions** for multi-model updates
2. **Check both status and is_locked** for critical operations
3. **Use database locks** for race condition prevention
4. **Provide clear user feedback** with Persian messages
5. **Log all lock/unlock operations** for audit trails
6. **Test edge cases** thoroughly with unit tests
7. **Monitor performance** of lock-related queries
8. **Keep status and is_locked synchronized** at all times

