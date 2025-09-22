<?php

namespace App\Providers;

use App\Events\BidAccepted;
use App\Events\BidOutbid;
use App\Events\BidPlaced;
use App\Events\ReceiptApproved;
use App\Events\ReceiptRejected;
use App\Listeners\SendBidAcceptedNotification;
use App\Listeners\SendBidOutbidNotification;
use App\Listeners\SendBidPlacedNotification;
use App\Listeners\SendReceiptApprovedNotification;
use App\Listeners\SendReceiptRejectedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Bid events
        BidPlaced::class => [
            SendBidPlacedNotification::class,
        ],
        BidOutbid::class => [
            SendBidOutbidNotification::class,
        ],
        BidAccepted::class => [
            SendBidAcceptedNotification::class,
        ],

        // Receipt events
        ReceiptApproved::class => [
            SendReceiptApprovedNotification::class,
        ],
        ReceiptRejected::class => [
            SendReceiptRejectedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

