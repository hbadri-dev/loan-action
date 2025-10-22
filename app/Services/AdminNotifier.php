<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AdminNotifier
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = '506B7652434743463851415072786F523935514C7548445979654E4A665A6B634E537757704F6F544647773D';
        $this->baseUrl = 'https://api.kavenegar.com/v1';
    }

    /**
     * Send SMS notification to all admins about user actions
     */
    public function notifyAdmin(string $action, string $userName, string $details = '', string $template = 'AdminNotifier'): void
    {
        try {
            // Get all admin users
            $admins = User::role('admin')->whereNotNull('phone')->get();

            if ($admins->isEmpty()) {
                Log::warning('AdminNotifier: No admin users found with phone numbers');
                return;
            }

            // Prepare the message with tokens
            $token = $userName; // User name as first token
            $token2 = $details ?: $action; // Action details as second token

            $sentCount = 0;
            $failedCount = 0;

            // Send SMS to all admins immediately using simple message
            foreach ($admins as $admin) {
                try {
                    // Create the message for Lookup API (clean tokens)
                    $token = $this->cleanToken($userName); // First token: user name
                    $token2 = $this->cleanToken($details); // Second token: action details

                    // Send SMS using Lookup API
                    $result = $this->sendLookupSMS($admin->phone, $token, $token2, $template);

                    if ($result) {
                        $sentCount++;
                        Log::info('AdminNotifier: SMS sent successfully to admin', [
                            'admin_id' => $admin->id,
                            'admin_phone' => $admin->phone,
                            'action' => $action,
                            'user_name' => $userName,
                            'details' => $details,
                            'template' => $template,
                            'token' => $token,
                            'token2' => $token2
                        ]);
                    } else {
                        $failedCount++;
                        Log::error('AdminNotifier: Failed to send SMS to admin', [
                            'admin_id' => $admin->id,
                            'admin_phone' => $admin->phone,
                            'action' => $action,
                            'user_name' => $userName,
                            'details' => $details,
                            'template' => $template,
                            'error' => 'Kavenegar service returned false'
                        ]);
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('AdminNotifier: Failed to send SMS to admin', [
                        'admin_id' => $admin->id,
                        'admin_phone' => $admin->phone,
                        'error' => $e->getMessage(),
                        'action' => $action,
                        'user_name' => $userName,
                        'details' => $details,
                        'template' => $template
                    ]);
                }
            }

            Log::info('AdminNotifier: SMS notification summary', [
                'total_admins' => $admins->count(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'action' => $action,
                'user_name' => $userName,
                'details' => $details,
                'template' => $template
            ]);

        } catch (\Exception $e) {
            Log::error('AdminNotifier: Failed to send SMS notifications to admins', [
                'error' => $e->getMessage(),
                'action' => $action,
                'user_name' => $userName,
                'details' => $details,
                'template' => $template
            ]);
        }
    }

    /**
     * Send SMS using Kavenegar Lookup API
     */
    private function sendLookupSMS(string $phone, string $token, string $token2, string $template = 'AdminNotifier'): bool
    {
        try {
            // Format phone number
            $formattedPhone = $this->formatPhone($phone);

            // Send real SMS in development
            if (false) { // Disable sandbox to send real SMS
                Log::info('AdminNotifier: SMS would be sent (Sandbox Mode)', [
                    'phone' => $phone,
                    'formatted_phone' => $formattedPhone,
                    'token' => $token,
                    'token2' => $token2,
                    'template' => $template
                ]);
                return true;
            }

            // Send SMS using Lookup API (verify/lookup.json)
            $response = $this->client->post("{$this->baseUrl}/{$this->apiKey}/verify/lookup.json", [
                'form_params' => [
                    'receptor' => $formattedPhone,
                    'token' => $token,
                    'token2' => $token2,
                    'template' => $template,
                ],
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['return']['status']) && $result['return']['status'] == 200) {
                Log::info('AdminNotifier: SMS sent successfully', [
                    'phone' => $phone,
                    'template' => $template,
                    'response' => $result
                ]);
                return true;
            } else {
                Log::error('AdminNotifier: SMS failed', [
                    'phone' => $phone,
                    'template' => $template,
                    'response' => $result
                ]);
                return false;
            }

        } catch (GuzzleException $e) {
            Log::error('AdminNotifier: SMS network error', [
                'phone' => $phone,
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('AdminNotifier: SMS error', [
                'phone' => $phone,
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format phone number for Kavenegar
     */
    private function formatPhone(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 98
        if (str_starts_with($phone, '0')) {
            $phone = '98' . substr($phone, 1);
        }

        // If doesn't start with 98, add it
        if (!str_starts_with($phone, '98')) {
            $phone = '98' . $phone;
        }

        return $phone;
    }

    /**
     * Clean token for Kavenegar (Persian-friendly approach)
     */
    private function cleanToken(string $token): string
    {
        // Remove only the most problematic characters for Kavenegar
        $token = str_replace([
            "\n", "\r", "\t",           // Newlines and tabs
            " ",                        // Only spaces (keep other separators)
            "'", '"', "`",              // Quotes
            "!", "@", "#", "$", "%",    // Special symbols
            "^", "&", "*", "+", "=",    // Math symbols
            "?", "<", ">",              // Comparison symbols
        ], "", $token);

        // Keep Persian punctuation and separators like : - _ ( )
        // Only remove truly problematic characters
        $token = preg_replace('/[^\p{L}\p{N}\p{P}\p{S}]/u', '', $token);

        // If token is empty, use a default
        if (empty($token)) {
            $token = 'کاربر';
        }

        // Limit to 100 characters as per Kavenegar API
        $token = substr($token, 0, 100);

        return $token;
    }

    /**
     * Notify admin about buyer actions
     * Enabled for: bid_placed, buyer_payment_completed
     */
    public function notifyBuyerAction(string $action, User $buyer, array $context = []): void
    {
        // Send notification for bid placement
        if ($action === 'bid_placed') {
            $bidAmount = number_format($context['bid_amount'] ?? 0);
            $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');
            
            // Use AdminBidPlaced template with specific tokens
            $this->notifyAdmin($action, $bidAmount, $auctionTitle, 'AdminBidPlaced');
        }
        
        // Send notification for buyer payment completion
        if ($action === 'buyer_payment_completed') {
            $buyerName = $this->cleanToken($buyer->name ?? 'خریدار');
            $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');
            
            // Use AdminBuyerPaymentCompleted template with two tokens
            $this->notifyAdmin($action, $buyerName, $auctionTitle, 'AdminBuyerPaymentCompleted');
        }
        
        // All other buyer actions are disabled
    }

    /**
     * Notify admin about seller actions
     * Enabled for: loan_verification_uploaded, bid_accepted
     */
    public function notifySellerAction(string $action, User $seller, array $context = []): void
    {
        // Send notification for loan verification upload
        if ($action === 'loan_verification_uploaded') {
            $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');
            $sellerName = $this->cleanToken($seller->name ?? 'فروشنده');

            // Use AdminLoanVerification template with specific tokens
            $this->notifyAdmin($action, $auctionTitle, $sellerName, 'AdminLoanVerification');
        }

        // Send notification for bid acceptance
        if ($action === 'bid_accepted') {
            $sellerName = $this->cleanToken($seller->name ?? 'فروشنده');
            $bidAmount = number_format($context['bid_amount'] ?? 0);
            $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');

            // Use AdminBidAccepted template with three tokens
            $this->notifyAdminWithThreeTokens($action, $sellerName, $bidAmount, $auctionTitle, 'AdminBidAccepted');
        }

        // All other seller actions are disabled
    }

    /**
     * Send SMS notification to all admins with three tokens
     */
    public function notifyAdminWithThreeTokens(string $action, string $token1, string $token2, string $token3, string $template = 'AdminNotifier'): void
    {
        try {
            // Get all admin users
            $admins = User::role('admin')->whereNotNull('phone')->get();

            if ($admins->isEmpty()) {
                Log::warning('AdminNotifier: No admin users found with phone numbers');
                return;
            }

            $sentCount = 0;
            $failedCount = 0;

            // Send SMS to all admins immediately
            foreach ($admins as $admin) {
                try {
                    // Clean tokens
                    $cleanToken1 = $this->cleanToken($token1);
                    $cleanToken2 = $this->cleanToken($token2);
                    $cleanToken3 = $this->cleanToken($token3);

                    // Send SMS using Lookup API with three tokens
                    $result = $this->sendLookupSMSWithThreeTokens($admin->phone, $cleanToken1, $cleanToken2, $cleanToken3, $template);

                    if ($result) {
                        $sentCount++;
                        Log::info('AdminNotifier: SMS sent successfully to admin', [
                            'admin_id' => $admin->id,
                            'admin_phone' => $admin->phone,
                            'action' => $action,
                            'template' => $template,
                            'token' => $cleanToken1,
                            'token2' => $cleanToken2,
                            'token3' => $cleanToken3
                        ]);
                    } else {
                        $failedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('AdminNotifier: Failed to send SMS to admin', [
                        'admin_id' => $admin->id,
                        'admin_phone' => $admin->phone,
                        'error' => $e->getMessage(),
                        'action' => $action,
                        'template' => $template
                    ]);
                }
            }

            Log::info('AdminNotifier: SMS notification summary', [
                'total_admins' => $admins->count(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'action' => $action,
                'template' => $template
            ]);

        } catch (\Exception $e) {
            Log::error('AdminNotifier: Failed to send SMS notifications to admins', [
                'error' => $e->getMessage(),
                'action' => $action,
                'template' => $template
            ]);
        }
    }

    /**
     * Send SMS using Kavenegar Lookup API with three tokens
     */
    private function sendLookupSMSWithThreeTokens(string $phone, string $token, string $token2, string $token3, string $template = 'AdminNotifier'): bool
    {
        try {
            // Format phone number
            $formattedPhone = $this->formatPhone($phone);

            // Send real SMS in development
            if (false) { // Disable sandbox to send real SMS
                Log::info('AdminNotifier: SMS would be sent (Sandbox Mode)', [
                    'phone' => $phone,
                    'formatted_phone' => $formattedPhone,
                    'token' => $token,
                    'token2' => $token2,
                    'token3' => $token3,
                    'template' => $template
                ]);
                return true;
            }

            // Send SMS using Lookup API (verify/lookup.json)
            $response = $this->client->post("{$this->baseUrl}/{$this->apiKey}/verify/lookup.json", [
                'form_params' => [
                    'receptor' => $formattedPhone,
                    'token' => $token,
                    'token2' => $token2,
                    'token3' => $token3,
                    'template' => $template,
                ],
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['return']['status']) && $result['return']['status'] == 200) {
                Log::info('AdminNotifier: SMS sent successfully', [
                    'phone' => $phone,
                    'template' => $template,
                    'response' => $result
                ]);
                return true;
            } else {
                Log::error('AdminNotifier: SMS failed', [
                    'phone' => $phone,
                    'template' => $template,
                    'response' => $result
                ]);
                return false;
            }

        } catch (GuzzleException $e) {
            Log::error('AdminNotifier: SMS network error', [
                'phone' => $phone,
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('AdminNotifier: SMS error', [
                'phone' => $phone,
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
