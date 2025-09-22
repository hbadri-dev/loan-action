<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Auction;
use App\Models\Bid;
use App\Enums\AuctionStatus;
use App\Enums\BidStatus;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 buyers with phone numbers, is_phone_verified=true
        $buyers = [];
        for ($i = 1; $i <= 3; $i++) {
            $buyer = User::firstOrCreate(
                ['email' => "buyer{$i}@example.com"],
                [
                    'name' => "خریدار {$i}",
                    'password' => bcrypt('password'),
                    'phone' => '0912345678' . $i,
                    'phone_verified_at' => now(),
                    'is_phone_verified' => true,
                ]
            );
            $buyer->assignRole('buyer');
            $buyers[] = $buyer;
        }

        // Create 2 sellers with phone numbers, is_phone_verified=true
        $sellers = [];
        for ($i = 1; $i <= 2; $i++) {
            $seller = User::firstOrCreate(
                ['email' => "seller{$i}@example.com"],
                [
                    'name' => "فروشنده {$i}",
                    'password' => bcrypt('password'),
                    'phone' => '0912345679' . $i,
                    'phone_verified_at' => now(),
                    'is_phone_verified' => true,
                ]
            );
            $seller->assignRole('seller');
            $sellers[] = $seller;
        }

        // Create 2 active auctions with different min_purchase_price
        $auction1 = Auction::create([
            'created_by' => $sellers[0]->id,
            'title' => 'مزایده وام اول',
            'description' => 'توضیحات مزایده وام اول',
            'loan_type' => 'personal',
            'principal_amount' => 50000000, // 50M Toman
            'term_months' => 24,
            'interest_rate_percent' => 25.5,
            'min_purchase_price' => 45000000, // 45M Toman
            'status' => AuctionStatus::ACTIVE,
        ]);

        $auction2 = Auction::create([
            'created_by' => $sellers[1]->id,
            'title' => 'مزایده وام دوم',
            'description' => 'توضیحات مزایده وام دوم',
            'loan_type' => 'business',
            'principal_amount' => 80000000, // 80M Toman
            'term_months' => 36,
            'interest_rate_percent' => 22.0,
            'min_purchase_price' => 75000000, // 75M Toman
            'status' => AuctionStatus::ACTIVE,
        ]);

        // Create a few sample bids
        Bid::create([
            'auction_id' => $auction1->id,
            'buyer_id' => $buyers[0]->id,
            'amount' => 46000000, // 46M Toman
            'status' => BidStatus::PENDING,
        ]);

        Bid::create([
            'auction_id' => $auction1->id,
            'buyer_id' => $buyers[1]->id,
            'amount' => 47000000, // 47M Toman
            'status' => BidStatus::PENDING,
        ]);

        Bid::create([
            'auction_id' => $auction2->id,
            'buyer_id' => $buyers[2]->id,
            'amount' => 76000000, // 76M Toman
            'status' => BidStatus::PENDING,
        ]);

        Bid::create([
            'auction_id' => $auction2->id,
            'buyer_id' => $buyers[0]->id,
            'amount' => 77000000, // 77M Toman
            'status' => BidStatus::HIGHEST,
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('Created 3 buyers, 2 sellers, 2 active auctions, and 4 bids');
    }
}
