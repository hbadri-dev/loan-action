<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearAuctionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:clear-all {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all auction data from the database and storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will permanently delete ALL auction data including auctions, bids, payments, receipts, and files. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('🗑️  Starting auction data cleanup...');

        try {
            // Clear auction-related data in correct order (respecting foreign key constraints)
            $this->clearAuctionData();

            $this->info('✅ All auction data has been successfully removed!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error clearing auction data: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clear all auction-related data
     */
    private function clearAuctionData()
    {
        $this->info('📋 Clearing auction-related tables...');

        // Clear in order of dependencies (child tables first)
        $tables = [
            'payment_transactions' => 'Payment transactions',
            'payments' => 'Payments',
            'payment_receipts' => 'Payment receipts',
            'loan_transfers' => 'Loan transfers',
            'buyer_progress' => 'Buyer progress',
            'seller_sales' => 'Seller sales',
            'contract_agreements' => 'Contract agreements',
            'bids' => 'Bids',
            'auctions' => 'Auctions',
        ];

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table => $description) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                DB::table($table)->truncate();
                $this->line("  ✓ Cleared {$count} {$description}");
            } else {
                $this->line("  - No {$description} to clear");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear file storage
        $this->clearFileStorage();

        // Reset auto-increment counters
        $this->resetAutoIncrement();
    }

    /**
     * Clear auction-related files from storage
     */
    private function clearFileStorage()
    {
        $this->info('📁 Clearing auction-related files...');

        try {
            // Clear receipt images
            if (Storage::disk('public')->exists('receipts')) {
                $receiptFiles = Storage::disk('public')->allFiles('receipts');
                Storage::disk('public')->delete($receiptFiles);
                $this->line("  ✓ Cleared " . count($receiptFiles) . " receipt files");
            }

            // Clear transfer receipt images
            if (Storage::disk('public')->exists('transfer-receipts')) {
                $transferFiles = Storage::disk('public')->allFiles('transfer-receipts');
                Storage::disk('public')->delete($transferFiles);
                $this->line("  ✓ Cleared " . count($transferFiles) . " transfer receipt files");
            }

            // Clear any other auction-related files
            if (Storage::disk('public')->exists('auctions')) {
                $auctionFiles = Storage::disk('public')->allFiles('auctions');
                Storage::disk('public')->delete($auctionFiles);
                $this->line("  ✓ Cleared " . count($auctionFiles) . " auction files");
            }

        } catch (\Exception $e) {
            $this->warn("  ⚠️  Could not clear some files: " . $e->getMessage());
        }
    }

    /**
     * Reset auto-increment counters
     */
    private function resetAutoIncrement()
    {
        $this->info('🔄 Resetting auto-increment counters...');

        $tables = [
            'auctions',
            'bids',
            'contract_agreements',
            'payment_receipts',
            'seller_sales',
            'loan_transfers',
            'buyer_progress',
            'payments',
            'payment_transactions',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            $this->line("  ✓ Reset {$table} auto-increment");
        }
    }
}
