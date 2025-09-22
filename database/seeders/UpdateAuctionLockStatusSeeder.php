<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Enums\AuctionStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAuctionLockStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update is_locked column based on status
        DB::table('auctions')->where('status', AuctionStatus::LOCKED->value)->update([
            'is_locked' => true
        ]);

        DB::table('auctions')->where('status', '!=', AuctionStatus::LOCKED->value)->update([
            'is_locked' => false
        ]);

        $this->command->info('Updated auction is_locked status based on auction status');
    }
}

