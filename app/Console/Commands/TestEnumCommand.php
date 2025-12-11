<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\ProductCategory;

class TestEnumCommand extends Command
{
    protected $signature = 'test:enum';
    protected $description = 'Test ProductCategory Enum';

    public function handle()
    {
        $this->info('Testing ProductCategory Enum...');
        
        $this->line('Cake Categories:');
        $this->table(['Category'], array_map(function($cat) {
            return [$cat];
        }, ProductCategory::getCakeCategories()));
        
        $this->line('Accessory Categories:');
        $this->table(['Category'], array_map(function($cat) {
            return [$cat];
        }, ProductCategory::getAccessoryCategories()));
        
        // Test with a specific product
        $testProduct = 'Chocolate Dream Cake';
        $testCategory = 'Chocolate Cake';
        
        $this->line("Testing with Product: {$testProduct}");
        $this->line("Category: {$testCategory}");
        
        // Use the new helper methods
        $isCake = ProductCategory::isCakeCategory($testCategory);
        $isAccessory = ProductCategory::isAccessoryCategory($testCategory);
        
        $this->line("Is Cake: " . ($isCake ? 'YES' : 'NO'));
        $this->line("Is Accessory: " . ($isAccessory ? 'YES' : 'NO'));
        
        return 0;
    }
}