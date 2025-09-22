# Persian UI Implementation Documentation

## Overview

This document describes the comprehensive Persian (Farsi) UI implementation for the loan auction system, including RTL support, localization, and custom components.

## Features Implemented

### 1. Persian Localization

#### Language Files

**`resources/lang/fa/messages.php`**

- Comprehensive Persian message translations
- Categorized by functionality (auth, auction, bid, payment, etc.)
- Success, error, warning, and info messages
- Button texts, navigation labels, and status messages
- Notification templates with placeholders

**`resources/lang/fa/validation.php`**

- Complete Persian validation messages
- Custom attribute names in Persian
- Form field labels and error messages
- Laravel validation rule translations

#### Message Categories

```php
// Authentication
__('messages.auth.login_success')
__('messages.auth.otp_sent')
__('messages.auth.access_denied')

// Auction
__('messages.auction.created')
__('messages.auction.locked')
__('messages.auction.locked_message')

// Bids
__('messages.bid.placed')
__('messages.bid.accepted')
__('messages.bid.invalid_amount')

// Payments
__('messages.payment.receipt_uploaded')
__('messages.payment.buyer_fee_amount')

// File Upload
__('messages.file.uploaded')
__('messages.file.file_too_large', ['max' => '5'])

// Steps
__('messages.steps.auction_details')
__('messages.steps.contract_text')
__('messages.steps.payment_fee')
```

### 2. RTL Layout Support

#### Base Layout Configuration

**`resources/views/layouts/app.blade.php`**

- `dir="rtl"` and `lang="fa"` attributes
- Vazirmatn font integration via Google Fonts
- Custom RTL CSS overrides
- Persian number formatting JavaScript

#### Tailwind RTL Utilities

**`tailwind.config.js`**

- Custom RTL utility classes
- Logical properties support (`margin-inline-start`, `padding-inline-end`)
- Direction-aware spacing (`space-x-reverse`)
- RTL-specific border and border-radius utilities

```css
/* RTL Utilities */
.text-start {
  text-align: start;
}
.text-end {
  text-align: end;
}
.float-start {
  float: start;
}
.float-end {
  float: end;
}
.me-auto {
  margin-inline-end: auto;
}
.ms-auto {
  margin-inline-start: auto;
}
.border-start {
  border-inline-start-width: 1px;
}
.border-end {
  border-inline-end-width: 1px;
}
```

#### Font Configuration

```css
body {
  font-family: 'Vazirmatn', sans-serif;
  font-weight: 400;
}
```

### 3. Stepper Component

#### Component Usage

**`<x-stepper>` Component**

```blade
<x-stepper
    :steps="[
        ['title' => 'جزئیات وام', 'description' => 'مشاهده اطلاعات وام'],
        ['title' => 'متن قرارداد', 'description' => 'تأیید متن قرارداد'],
        ['title' => 'پرداخت کارمزد', 'description' => 'واریز کارمزد ۳ میلیون تومان']
    ]"
    :current-step="2"
    :completed-steps="[1]"
    variant="default"
    :show-numbers="true"
/>
```

#### Variants

**Horizontal (default):**

- Linear progress indicator
- Step numbers with connecting lines
- Compact and detailed modes

**Vertical:**

- Stacked step layout
- Detailed descriptions
- Status indicators

**Compact:**

- Condensed horizontal layout
- Grid-based title display
- Space-efficient design

#### Props

- `steps`: Array of step definitions
- `currentStep`: Current active step number
- `completedSteps`: Array of completed step numbers
- `variant`: 'default', 'compact', 'vertical'
- `showNumbers`: Display step numbers (default: true)
- `class`: Additional CSS classes

### 4. Amount Input Component

#### Component Usage

**`<x-amount-input>` Component**

```blade
<x-amount-input
    name="bid_amount"
    label="مبلغ پیشنهاد"
    placeholder="مبلغ پیشنهادی خود را وارد کنید..."
    :required="true"
    :min="5000000"
    :max="100000000"
    :step="1000000"
    currency="تومان"
    :show-currency="true"
    :show-thousand-separator="true"
    :persian-numbers="true"
/>
```

#### Features

**Number Formatting:**

- Thousand separators (1,000,000)
- Persian number conversion (۱۲۳۴۵۶۷۸۹۰)
- Currency display (تومان)
- Real-time formatting

**Validation:**

- Minimum/maximum amount validation
- Step validation (must be multiple of step value)
- Custom error messages in Persian
- Real-time validation feedback

**User Experience:**

- Focus/blur formatting
- Paste handling
- Keyboard shortcuts (Ctrl+Arrow keys)
- Visual feedback and help text

#### Props

- `name`: Input field name
- `label`: Field label
- `placeholder`: Input placeholder
- `required`: Required field validation
- `min`: Minimum allowed value
- `max`: Maximum allowed value
- `step`: Step increment value
- `currency`: Currency symbol/text
- `showCurrency`: Display currency text
- `showThousandSeparator`: Format with commas
- `persianNumbers`: Convert to Persian digits

### 5. Localization Service

#### Service Usage

**`LocalizationService`**

```php
$localization = app(LocalizationService::class);

// Get messages
$message = $localization->getMessage('bid.placed');
$error = $localization->getErrorMessage('validation');

// Format numbers
$persianNumber = $localization->toPersianNumbers('123456789');
$englishNumber = $localization->toEnglishNumbers('۱۲۳۴۵۶۷۸۹');

// Format amounts
$formatted = $localization->formatAmount(15000000, 'تومان', true);
$parsed = $localization->parseAmount('۱۵,۰۰۰,۰۰۰ تومان');

// Get step titles
$stepTitle = $localization->getStepTitle(3, 'buyer');
$stepTitle = $localization->getStepTitle(4, 'seller');
```

#### Methods

**Message Retrieval:**

- `getMessage()`: Get localized message
- `getValidationMessage()`: Get validation message
- `getAttributeName()`: Get attribute name
- `getErrorMessage()`: Get error message
- `getSuccessMessage()`: Get success message

**Number Formatting:**

- `toPersianNumbers()`: Convert to Persian digits
- `toEnglishNumbers()`: Convert to English digits
- `formatAmount()`: Format amount with currency
- `parseAmount()`: Parse formatted amount

**Step Management:**

- `getStepTitle()`: Get step title by number and type
- `getBuyerStepKey()`: Get buyer step key
- `getSellerStepKey()`: Get seller step key

**Utility Methods:**

- `getDirectionClass()`: Get RTL-aware CSS class
- `getTextAlignClass()`: Get text alignment class
- `getSpacingClasses()`: Get direction-aware spacing classes

### 6. RTL-Specific Features

#### CSS Customizations

```css
/* Form styling for RTL */
.form-input,
.form-select,
.form-textarea {
  text-align: right;
}

/* Button alignment */
.btn-group {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-start;
}

/* Table styling for RTL */
.table th,
.table td {
  text-align: right;
}

/* Navigation alignment */
.nav-link {
  text-align: right;
}
```

#### JavaScript Features

**Persian Number Formatting:**

```javascript
function formatPersianNumber(num) {
  const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
  const englishDigits = '0123456789';

  return num.toString().replace(/[0-9]/g, function (w) {
    return persianDigits[englishDigits.indexOf(w)];
  });
}
```

**Auto-formatting:**

- Automatic Persian number conversion for elements with `.persian-numbers` class
- Real-time formatting in amount inputs
- Date and time formatting

### 7. Component Integration

#### Form Integration

```blade
<form class="space-y-6" dir="rtl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-amount-input
            name="auction_amount"
            label="مبلغ مزایده"
            :required="true"
            :min="10000000"
            currency="تومان"
        />

        <x-file-upload
            name="receipt_image"
            label="رسید پرداخت"
            :required="true"
            :preview="true"
        />
    </div>

    <x-stepper
        :steps="$auctionSteps"
        :current-step="$currentStep"
        :completed-steps="$completedSteps"
        variant="default"
    />
</form>
```

#### Validation Integration

```php
// In Form Request
public function messages()
{
    return [
        'amount.required' => __('messages.form.required'),
        'amount.min' => __('messages.form.amount_too_small', ['min' => $this->min]),
        'receipt_image.required' => __('messages.file.image_required'),
    ];
}

// In Controller
$message = app(LocalizationService::class)->getMessage('bid.placed');
return redirect()->back()->with('success', $message);
```

### 8. Usage Examples

#### Buyer Auction Flow

```blade
<!-- Step 1: Auction Details -->
<x-stepper
    :steps="$buyerSteps"
    :current-step="1"
    :completed-steps="[]"
/>

<!-- Step 4: Place Bid -->
<x-amount-input
    name="bid_amount"
    label="مبلغ پیشنهاد"
    :min="$auction->min_purchase_price"
    :step="1000000"
    currency="تومان"
/>

<!-- Step 3: Payment Receipt -->
<x-file-upload
    name="receipt_image"
    label="رسید پرداخت کارمزد"
    :required="true"
/>
```

#### Seller Sale Flow

```blade
<!-- Seller Steps -->
<x-stepper
    :steps="$sellerSteps"
    :current-step="4"
    :completed-steps="[1, 2, 3]"
    variant="compact"
/>

<!-- Bid Acceptance -->
<div class="text-center">
    <p class="text-lg font-medium mb-4">
        بالاترین پیشنهاد:
        <span class="persian-numbers">{{ number_format($highestBid->amount) }}</span> تومان
    </p>
    <button class="bg-green-500 text-white px-6 py-2 rounded">
        {{ __('messages.buttons.accept_bid') }}
    </button>
</div>
```

### 9. Best Practices

#### RTL Layout

1. **Always use `dir="rtl"`** on form and container elements
2. **Use logical properties** (`margin-inline-start` instead of `margin-left`)
3. **Reverse flex directions** with `space-x-reverse` class
4. **Right-align text** with `text-right` or `text-start`
5. **Use Persian numbers** with `.persian-numbers` class

#### Localization

1. **Always use translation functions** (`__()`, `trans()`)
2. **Provide context** in translation keys
3. **Use placeholders** for dynamic content
4. **Group related messages** in logical categories
5. **Test all translations** for accuracy

#### Components

1. **Use semantic props** for configuration
2. **Provide sensible defaults** for optional props
3. **Include validation** and error handling
4. **Make components accessible** with proper ARIA labels
5. **Test responsive behavior** on different screen sizes

#### Performance

1. **Lazy load** heavy components
2. **Debounce** input formatting
3. **Cache** translation lookups
4. **Minimize** JavaScript for number formatting
5. **Optimize** CSS for RTL layouts

### 10. Testing

#### Component Testing

```php
/** @test */
public function stepper_displays_correct_current_step()
{
    $steps = [
        ['title' => 'مرحله ۱', 'description' => 'توضیحات'],
        ['title' => 'مرحله ۲', 'description' => 'توضیحات']
    ];

    $view = $this->blade('<x-stepper :steps="$steps" :current-step="2" />', compact('steps'));

    $view->assertSee('مرحله ۲');
    $view->assertSee('bg-blue-500'); // Current step styling
}

/** @test */
public function amount_input_formats_numbers_correctly()
{
    $view = $this->blade('<x-amount-input name="amount" />');

    $view->assertSee('مبلغ');
    $view->assertSee('تومان');
}
```

#### Localization Testing

```php
/** @test */
public function persian_messages_are_loaded()
{
    $this->app->setLocale('fa');

    $this->assertEquals('عملیات با موفقیت انجام شد.', __('messages.success'));
    $this->assertEquals('خطایی رخ داده است.', __('messages.error'));
}

/** @test */
public function validation_messages_are_in_persian()
{
    $this->app->setLocale('fa');

    $this->assertEquals('فیلد نام الزامی است.', __('validation.required', ['attribute' => 'نام']));
}
```

### 11. Demo Page

Access the demo page at `/demo/persian-ui` to see all components in action:

- **Stepper variants** (horizontal, compact, vertical)
- **Amount input examples** with different configurations
- **File upload components** with validation
- **Persian messages** and translations
- **RTL layout** demonstrations
- **Complete form example** with all components

### 12. Browser Support

#### RTL Support

- ✅ Chrome 88+
- ✅ Firefox 85+
- ✅ Safari 14+
- ✅ Edge 88+

#### Font Support

- ✅ Vazirmatn font via Google Fonts
- ✅ Fallback to system fonts
- ✅ Persian number rendering

#### JavaScript Features

- ✅ ES6+ features with Babel transpilation
- ✅ Modern browser APIs
- ✅ Responsive design support

This comprehensive Persian UI implementation provides a fully localized, RTL-compatible interface for the loan auction system with modern components and excellent user experience.

