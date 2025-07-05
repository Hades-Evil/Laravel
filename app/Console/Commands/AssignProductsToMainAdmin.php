<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class AssignProductsToMainAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:assign-to-main-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all products without user_id to the main admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mainAdmin = User::where('email', 'admin2@gmail.com')->first();
        
        if (!$mainAdmin) {
            $this->error('Main admin not found!');
            return 1;
        }
        
        $updatedCount = Product::whereNull('user_id')->update(['user_id' => $mainAdmin->id]);
        
        $this->info("Updated {$updatedCount} products to be owned by main admin.");
        
        return 0;
    }
}
