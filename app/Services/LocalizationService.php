<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LocalizationService
{
    /**
     * Get localized message
     */
    public function getMessage(string $key, array $replace = []): string
    {
        return __("messages.{$key}", $replace);
    }

    /**
     * Get localized validation message
     */
    public function getValidationMessage(string $key, array $replace = []): string
    {
        return __("validation.{$key}", $replace);
    }

    /**
     * Get localized attribute name
     */
    public function getAttributeName(string $attribute): string
    {
        return __("validation.attributes.{$attribute}");
    }

    /**
     * Format Persian numbers
     */
    public function toPersianNumbers(string $text): string
    {
        $persianDigits = '۰۱۲۳۴۵۶۷۸۹';
        $englishDigits = '0123456789';

        return str_replace($englishDigits, $persianDigits, $text);
    }

    /**
     * Format English numbers from Persian
     */
    public function toEnglishNumbers(string $text): string
    {
        $persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $arabicIndicDigits = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $englishDigits = ['0','1','2','3','4','5','6','7','8','9'];

        // Replace Persian digits
        $text = str_replace($persianDigits, $englishDigits, $text);
        // Replace Arabic-Indic digits
        $text = str_replace($arabicIndicDigits, $englishDigits, $text);

        // Normalize Arabic tatweel and other possible separators
        $text = str_replace(["\xE2\x80\x8C", "\xD9\x80"], '', $text); // ZWNJ, Tatweel

        return $text;
    }

    /**
     * Format amount with thousand separators and Persian numbers
     */
    public function formatAmount(int $amount, string $currency = 'تومان', bool $persianNumbers = true): string
    {
        $formatted = number_format($amount);

        if ($persianNumbers) {
            $formatted = $this->toPersianNumbers($formatted);
        }

        return $formatted . ' ' . $currency;
    }

    /**
     * Parse amount from formatted string
     */
    public function parseAmount(string $formattedAmount): int
    {
        // Remove currency text
        $amount = preg_replace('/[^\d,]/', '', $formattedAmount);

        // Convert Persian numbers to English
        $amount = $this->toEnglishNumbers($amount);

        // Remove thousand separators
        $amount = str_replace(',', '', $amount);

        return (int) $amount;
    }

    /**
     * Get step title
     */
    public function getStepTitle(int $stepNumber, string $stepType = 'buyer'): string
    {
        $stepKey = match($stepType) {
            'buyer' => $this->getBuyerStepKey($stepNumber),
            'seller' => $this->getSellerStepKey($stepNumber),
            default => "steps.step_{$stepNumber}"
        };

        return $this->getMessage($stepKey);
    }

    /**
     * Get buyer step key
     */
    protected function getBuyerStepKey(int $stepNumber): string
    {
        return match($stepNumber) {
            1 => 'steps.auction_details',
            2 => 'steps.contract_text',
            3 => 'steps.payment_fee',
            4 => 'steps.place_bid',
            5 => 'steps.waiting_seller',
            6 => 'steps.purchase_payment',
            7 => 'steps.loan_transfer',
            8 => 'steps.complete',
            default => "steps.step_{$stepNumber}"
        };
    }

    /**
     * Get seller step key
     */
    protected function getSellerStepKey(int $stepNumber): string
    {
        return match($stepNumber) {
            1 => 'steps.sale_details',
            2 => 'steps.seller_contract',
            3 => 'steps.seller_payment_fee',
            4 => 'steps.bid_acceptance',
            5 => 'steps.awaiting_buyer_payment',
            6 => 'steps.seller_loan_transfer',
            7 => 'steps.awaiting_transfer_confirmation',
            8 => 'steps.sale_completion',
            default => "steps.step_{$stepNumber}"
        };
    }

    /**
     * Get status message
     */
    public function getStatusMessage(string $status): string
    {
        return $this->getMessage("status.{$status}");
    }

    /**
     * Get button text
     */
    public function getButtonText(string $button): string
    {
        return $this->getMessage("buttons.{$button}");
    }

    /**
     * Get navigation text
     */
    public function getNavigationText(string $nav): string
    {
        return $this->getMessage("navigation.{$nav}");
    }

    /**
     * Get error message
     */
    public function getErrorMessage(string $error): string
    {
        return $this->getMessage("errors.{$error}");
    }

    /**
     * Get success message
     */
    public function getSuccessMessage(string $success): string
    {
        return $this->getMessage("success_messages.{$success}");
    }

    /**
     * Get notification message
     */
    public function getNotificationMessage(string $notification, array $replace = []): string
    {
        return $this->getMessage("notifications.{$notification}", $replace);
    }

    /**
     * Format date in Persian
     */
    public function formatDate(\DateTime $date, string $format = 'Y/m/d'): string
    {
        // This would require a Persian date library like jdf
        // For now, return regular format
        return $date->format($format);
    }

    /**
     * Format time in Persian
     */
    public function formatTime(\DateTime $time, string $format = 'H:i'): string
    {
        $formatted = $time->format($format);
        return $this->toPersianNumbers($formatted);
    }

    /**
     * Get relative time in Persian
     */
    public function getRelativeTime(\DateTime $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->days > 0) {
            return $this->toPersianNumbers($diff->days) . ' روز پیش';
        } elseif ($diff->h > 0) {
            return $this->toPersianNumbers($diff->h) . ' ساعت پیش';
        } elseif ($diff->i > 0) {
            return $this->toPersianNumbers($diff->i) . ' دقیقه پیش';
        } else {
            return 'همین الان';
        }
    }

    /**
     * Get plural form in Persian
     */
    public function getPlural(string $singular, int $count): string
    {
        // Persian doesn't have plural forms like English
        return $singular;
    }

    /**
     * Get gender-specific text
     */
    public function getGenderText(string $key, string $gender = 'neutral'): string
    {
        // Persian has gender-specific forms
        $genderKey = $gender === 'female' ? "{$key}_female" : "{$key}_male";

        if (trans("messages.{$genderKey}") !== "messages.{$genderKey}") {
            return $this->getMessage($genderKey);
        }

        return $this->getMessage($key);
    }

    /**
     * Get direction-aware CSS class
     */
    public function getDirectionClass(string $baseClass): string
    {
        return App::getLocale() === 'fa' ? "{$baseClass}-rtl" : $baseClass;
    }

    /**
     * Get text alignment class
     */
    public function getTextAlignClass(): string
    {
        return App::getLocale() === 'fa' ? 'text-right' : 'text-left';
    }

    /**
     * Get margin/padding direction classes
     */
    public function getSpacingClasses(): array
    {
        return App::getLocale() === 'fa' ? [
            'start' => 'me',
            'end' => 'ms',
            'left' => 'me',
            'right' => 'ms'
        ] : [
            'start' => 'ms',
            'end' => 'me',
            'left' => 'ms',
            'right' => 'me'
        ];
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(): string
    {
        return 'تومان';
    }

    /**
     * Get currency code
     */
    public function getCurrencyCode(): string
    {
        return 'IRR';
    }

    /**
     * Get number format
     */
    public function getNumberFormat(): array
    {
        return [
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'تومان',
            'currency_position' => 'after'
        ];
    }
}
