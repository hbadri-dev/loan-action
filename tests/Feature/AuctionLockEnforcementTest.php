<?php

namespace Tests\Feature;

use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Services\AuctionLockService;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionLockEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Auction $auction;
    protected BiddingService $biddingService;
    protected AuctionLockService $auctionLockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->auction = Auction::factory()->create([
            'user_id' => $this->seller->id,
            'status' => AuctionStatus::ACTIVE,
            'is_locked' => false,
        ]);

        $this->biddingService = new BiddingService();
        $this->auctionLockService = new AuctionLockService();
    }

    /** @test */
    public function it_prevents_bidding_on_locked_auction()
    {
        // Lock the auction
        $this->auction->update([
            'status' => AuctionStatus::LOCKED,
            'is_locked' => true,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('مزایده قفل است.');

        $this->biddingService->placeBid($this->auction, $this->buyer, 1000000);
    }

    /** @test */
    public function it_prevents_bidding_on_inactive_auction()
    {
        // Set auction as inactive
        $this->auction->update([
            'status' => AuctionStatus::PAUSED,
            'is_locked' => false,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('مزایده در حال حاضر فعال نیست.');

        $this->biddingService->placeBid($this->auction, $this->buyer, 1000000);
    }

    /** @test */
    public function it_allows_bidding_on_active_unlocked_auction()
    {
        $this->assertFalse($this->auction->isLocked());
        $this->assertEquals(AuctionStatus::ACTIVE, $this->auction->status);

        // This should not throw an exception
        $bid = $this->biddingService->placeBid($this->auction, $this->buyer, 1000000);

        $this->assertNotNull($bid);
        $this->assertEquals(BidStatus::HIGHEST, $bid->status);
    }

    /** @test */
    public function it_sets_all_bids_to_rejected_when_accepting_highest()
    {
        // Create multiple bids
        $bid1 = Bid::factory()->create([
            'auction_id' => $this->auction->id,
            'user_id' => $this->buyer->id,
            'amount' => 1000000,
            'status' => BidStatus::HIGHEST,
        ]);

        $bid2 = Bid::factory()->create([
            'auction_id' => $this->auction->id,
            'user_id' => User::factory()->create(['role' => 'buyer'])->id,
            'amount' => 900000,
            'status' => BidStatus::OUTBID,
        ]);

        $bid3 = Bid::factory()->create([
            'auction_id' => $this->auction->id,
            'user_id' => User::factory()->create(['role' => 'buyer'])->id,
            'amount' => 800000,
            'status' => BidStatus::PENDING,
        ]);

        // Accept the highest bid
        $result = $this->auctionLockService->lockOnAcceptance($this->auction, $bid1);

        // Refresh models from database
        $bid1->refresh();
        $bid2->refresh();
        $bid3->refresh();

        // Check that accepted bid is accepted
        $this->assertEquals(BidStatus::ACCEPTED, $bid1->status);

        // Check that other bids are rejected
        $this->assertEquals(BidStatus::REJECTED, $bid2->status);
        $this->assertEquals(BidStatus::REJECTED, $bid3->status);

        // Check that auction is locked
        $this->assertTrue($this->auction->fresh()->isLocked());
        $this->assertEquals(AuctionStatus::LOCKED, $this->auction->fresh()->status);
    }

    /** @test */
    public function it_syncs_is_locked_with_status()
    {
        // Test setting status to LOCKED
        $this->auction->status = AuctionStatus::LOCKED;
        $this->auction->save();

        $this->assertTrue($this->auction->fresh()->is_locked);
        $this->assertTrue($this->auction->fresh()->isLocked());

        // Test setting status to ACTIVE
        $this->auction->status = AuctionStatus::ACTIVE;
        $this->auction->save();

        $this->assertFalse($this->auction->fresh()->is_locked);
        $this->assertFalse($this->auction->fresh()->isLocked());
    }

    /** @test */
    public function it_can_unlock_auction()
    {
        // Lock the auction first
        $this->auction->update([
            'status' => AuctionStatus::LOCKED,
            'is_locked' => true,
        ]);

        // Create an accepted bid
        $bid = Bid::factory()->create([
            'auction_id' => $this->auction->id,
            'user_id' => $this->buyer->id,
            'amount' => 1000000,
            'status' => BidStatus::ACCEPTED,
        ]);

        // Unlock the auction
        $unlockedAuction = $this->auctionLockService->unlock($this->auction, 'Test unlock');

        $this->assertFalse($unlockedAuction->isLocked());
        $this->assertEquals(AuctionStatus::ACTIVE, $unlockedAuction->status);
        $this->assertFalse($unlockedAuction->is_locked);

        // Check that accepted bid is now highest again
        $bid->refresh();
        $this->assertEquals(BidStatus::HIGHEST, $bid->status);
    }

    /** @test */
    public function it_can_force_lock_auction()
    {
        $this->assertEquals(AuctionStatus::ACTIVE, $this->auction->status);
        $this->assertFalse($this->auction->is_locked);

        // Force lock the auction
        $lockedAuction = $this->auctionLockService->forceLock($this->auction, 'Emergency lock');

        $this->assertTrue($lockedAuction->isLocked());
        $this->assertEquals(AuctionStatus::LOCKED, $lockedAuction->status);
        $this->assertTrue($lockedAuction->is_locked);
    }

    /** @test */
    public function it_provides_lock_information()
    {
        $lockInfo = $this->auctionLockService->getLockInfo($this->auction);

        $this->assertArrayHasKey('is_locked', $lockInfo);
        $this->assertArrayHasKey('locked_at', $lockInfo);
        $this->assertArrayHasKey('accepted_bid', $lockInfo);
        $this->assertArrayHasKey('seller_sale', $lockInfo);

        $this->assertFalse($lockInfo['is_locked']);
        $this->assertNull($lockInfo['locked_at']);
        $this->assertNull($lockInfo['accepted_bid']);
    }
}

