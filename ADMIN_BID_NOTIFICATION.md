# Admin Bid Notification Implementation

## Overview

When a buyer submits a bid, an SMS notification is immediately sent to all admin users using Kavenegar's `AdminBidPlaced` lookup template.

## Kavenegar Template Configuration

### Template Name

`AdminBidPlaced`

### Template Content

```
ادمین گرامی
یک پیشنهاد جدید به مبلغ %token تومان در مزایده "%token2" توسط خریدار ثبت شد.

nationalkind.ir
وام یار
```

### Tokens

- `%token`: Bid amount (formatted with commas, e.g., "1,000,000")
- `%token2`: Auction title

## Implementation Details

### Modified Files

#### 1. `app/Services/AdminNotifier.php`

**Changes:**

- Added `$template` parameter to `notifyAdmin()` method (defaults to 'AdminNotifier')
- Added `$template` parameter to `sendLookupSMS()` method
- Updated `notifyBuyerAction()` to only send notifications for `bid_placed` action
- Disabled all other buyer action notifications
- Disabled all seller action notifications via `notifySellerAction()`

**Key Code:**

```php
public function notifyBuyerAction(string $action, User $buyer, array $context = []): void
{
    // Only send notification for bid placement
    if ($action === 'bid_placed') {
        $bidAmount = number_format($context['bid_amount'] ?? 0);
        $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');

        // Use AdminBidPlaced template with specific tokens
        $this->notifyAdmin($action, $bidAmount, $auctionTitle, 'AdminBidPlaced');
    }

    // All other buyer actions are disabled
}
```

#### 2. `app/Http/Controllers/Buyer/BidController.php`

**Existing Code (No Changes Required):**

```php
// Notify admin about bid placement
$this->adminNotifier->notifyBuyerAction('bid_placed', $user, [
    'auction_title' => $auction->title,
    'bid_amount' => $request->amount
]);
```

This code at line 70-73 already triggers the notification when a buyer submits a bid.

## Flow

1. Buyer submits a bid via `BidController::submitBid()`
2. Controller calls `AdminNotifier::notifyBuyerAction('bid_placed', ...)`
3. `notifyBuyerAction()` formats the bid amount and auction title
4. Calls `notifyAdmin()` with template name `AdminBidPlaced`
5. `notifyAdmin()` retrieves all admin users with phone numbers
6. For each admin, calls `sendLookupSMS()` with the template
7. SMS is sent via Kavenegar Lookup API to all admins

## Testing

### Sandbox Mode

The sandbox mode is currently disabled (`if (false)`). To enable it for testing without sending real SMS:

```php
// In AdminNotifier.php, line 123
if (true) { // Enable sandbox mode
```

### Production Mode

Ensure the following:

1. Kavenegar template `AdminBidPlaced` is created and approved
2. Template has exactly 2 tokens: `%token` and `%token2`
3. API key is correctly configured in the service
4. Admin users have valid phone numbers in the database

## Disabled Notifications

The following notifications have been disabled as requested:

### Buyer Actions (Disabled)

- `payment_receipt_uploaded`
- `purchase_payment_uploaded`
- `loan_transfer_confirmed`

### Seller Actions (All Disabled)

- `bid_accepted`
- `sale_created`
- `loan_transfer_uploaded`
- `auction_created`

Only `bid_placed` action sends notifications to admin.

## Monitoring

Check logs for notification status:

```bash
tail -f storage/logs/laravel.log | grep "AdminNotifier"
```

Successful SMS:

```
AdminNotifier: SMS sent successfully to admin
```

Failed SMS:

```
AdminNotifier: Failed to send SMS to admin
```

## Notes

- SMS is sent immediately (not queued) when bid is placed
- All admins with phone numbers receive the notification
- Phone numbers are automatically formatted to international format (98...)
- Auction titles are cleaned to remove problematic characters for SMS
- Bid amounts are formatted with thousand separators

