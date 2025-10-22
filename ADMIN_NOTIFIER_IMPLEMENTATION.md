# Admin Notifier SMS Implementation

## Overview

This implementation adds SMS notifications to the admin whenever buyers or sellers perform key actions in the auction system. The notifications use the Kavenegar SMS service with the `AdminNotifier` template.

## Template Configuration

**Template Name**: `AdminNotifier`
**Template Content**:

```
ادمین عزیز
اقدام جدید زیر توسط %token انجام شد.
%token2

NationalKind.ir
```

**Token Usage**:

- `%token`: User name (buyer or seller)
- `%token2`: Action details (formatted message about the specific action)

## Implementation Details

### 1. AdminNotifier Service

**Location**: `app/Services/AdminNotifier.php`

The service provides methods to notify admin about user actions:

- `notifyAdmin(string $action, string $userName, string $details)`: Core notification method
- `notifyBuyerAction(string $action, User $buyer, array $context)`: Buyer-specific notifications
- `notifySellerAction(string $action, User $seller, array $context)`: Seller-specific notifications

### 2. Buyer Actions Notified

#### Bid Placement

- **Trigger**: When a buyer places a bid
- **Controller**: `app/Http/Controllers/Buyer/BidController.php`
- **Method**: `submitBid()`
- **Message**: "پیشنهاد جدید در مزایده: {auction_title} - مبلغ: {bid_amount} تومان"

#### Payment Receipt Upload

- **Trigger**: When a buyer uploads payment receipt
- **Controller**: `app/Http/Controllers/Buyer/ReceiptController.php`
- **Method**: `uploadPaymentReceipt()`
- **Message**: "آپلود رسید پرداخت کارمزد برای مزایده: {auction_title}"

#### Purchase Payment Upload

- **Trigger**: When a buyer uploads purchase payment receipt
- **Controller**: `app/Http/Controllers/Buyer/AuctionFlowController.php`
- **Method**: `uploadPurchasePayment()`
- **Message**: "آپلود رسید پرداخت مبلغ خرید برای مزایده: {auction_title} - مبلغ: {amount} تومان"

#### Loan Transfer Confirmation

- **Trigger**: When a buyer confirms loan transfer
- **Controller**: `app/Http/Controllers/Buyer/AuctionFlowController.php`
- **Method**: `confirmLoanTransfer()`
- **Message**: "تأیید انتقال وام برای مزایده: {auction_title}"

### 3. Seller Actions Notified

#### Sale Creation

- **Trigger**: When a seller starts a sale process
- **Controller**: `app/Http/Controllers/Seller/SaleFlowController.php`
- **Method**: `startSale()`
- **Message**: "ایجاد فروش جدید برای مزایده: {auction_title}"

#### Bid Acceptance

- **Trigger**: When a seller accepts a bid
- **Controller**: `app/Http/Controllers/Seller/SaleFlowController.php`
- **Method**: `acceptBid()`
- **Message**: "پذیرش پیشنهاد در مزایده: {auction_title} - خریدار: {buyer_name} - مبلغ: {bid_amount} تومان"

#### Loan Transfer Upload

- **Trigger**: When a seller uploads loan transfer receipt
- **Controller**: `app/Http/Controllers/Seller/SaleFlowController.php`
- **Method**: `uploadLoanTransferReceipt()`
- **Message**: "آپلود فیش انتقال وام برای مزایده: {auction_title}"

## Technical Implementation

### Dependencies Added

All controllers now inject the `AdminNotifier` service:

```php
use App\Services\AdminNotifier;

protected AdminNotifier $adminNotifier;

public function __construct(AdminNotifier $adminNotifier)
{
    $this->adminNotifier = $adminNotifier;
}
```

### SMS Sending

The service uses Kavenegar Lookup API with proper token structure:

```php
// Get all admin users with phone numbers
$admins = User::role('admin')->whereNotNull('phone')->get();

// Send SMS using Lookup API for each admin
foreach ($admins as $admin) {
    $token = $userName;        // First token: user name
    $token2 = $details;        // Second token: action details

    // Send via Kavenegar Lookup API
    $response = $client->post("https://api.kavenegar.com/v1/{API_KEY}/verify/lookup.json", [
        'form_params' => [
            'receptor' => $formattedPhone,
            'token' => $token,
            'token2' => $token2,
            'template' => 'AdminNotifier',
        ]
    ]);
}
```

### Error Handling

- Comprehensive logging for successful and failed SMS sends
- Graceful handling when no admin users are found
- Individual error handling for each admin (if one fails, others still get notified)
- Exception handling to prevent application crashes
- Summary logging showing total admins, successful sends, and failures

## Usage Examples

### Buyer Places Bid

```php
$this->adminNotifier->notifyBuyerAction('bid_placed', $user, [
    'auction_title' => $auction->title,
    'bid_amount' => $request->amount
]);
```

### Seller Accepts Bid

```php
$this->adminNotifier->notifySellerAction('bid_accepted', $user, [
    'auction_title' => $auction->title,
    'bid_amount' => $highestBid->amount,
    'buyer_name' => $highestBid->buyer->name
]);
```

## Configuration Requirements

1. **Admin Users**: All admin users must have valid phone numbers
2. **Multiple Admins**: The system will automatically send notifications to all users with 'admin' role
3. **Development Mode**: Currently logs messages instead of sending actual SMS
4. **Production Setup**: To enable actual SMS sending, configure Kavenegar API properly

## Current Status

✅ **System Working**: All notifications are properly logged and tracked
✅ **Message Format**: Persian messages with proper token structure
✅ **Admin Detection**: Automatically finds all admin users
✅ **Action Tracking**: Comprehensive logging of all user actions
✅ **Kavenegar Lookup API**: Uses correct Lookup API with tokens
✅ **Template Support**: Ready for AdminNotifier template

⚠️ **SMS Delivery**: Currently in sandbox mode due to API key length issue
⚠️ **Production**: API key needs to be shortened for production use

## Testing

A test file `test_admin_notifier.php` has been created to verify the service functionality.

## Benefits

1. **Real-time Monitoring**: Admin receives immediate notifications about all user actions
2. **Audit Trail**: SMS notifications provide a record of all system activities
3. **Proactive Management**: Admin can respond quickly to important events
4. **User Accountability**: Users are aware that their actions are monitored

## Future Enhancements

1. **Action Filtering**: Allow admin to configure which actions trigger notifications
2. **Action Grouping**: Batch similar actions to reduce SMS volume
3. **Priority Levels**: Different notification levels for different action types
4. **Admin Preferences**: Allow individual admins to opt-out of certain notifications
