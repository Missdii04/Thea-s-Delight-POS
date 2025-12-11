<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Container\BindingResolutionException; // Import for explicit catch

class ResetUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-users {--seed : Run the DatabaseSeeder after truncate}'; // Updated description

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the users table and optionally re-runs the DatabaseSeeder, bypassing foreign key checks.'; // Updated description

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user table reset process...');
        
        // 1. Disable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $this->warn('Foreign key checks disabled temporarily.');

        try {
            // 2. Truncate the users table
            DB::table('users')->truncate();
            $this->info('Users table truncated successfully. Auto-increment reset to 1.');

        } catch (\Exception $e) {
            $this->error('An error occurred during TRUNCATE: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            return Command::FAILURE;
        }

        // 3. Re-enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        $this->warn('Foreign key checks re-enabled.');
        
        // 4. Run the seeder if the --seed option was used
        if ($this->option('seed')) {
            $this->info('Attempting to run DatabaseSeeder...');
            
            try {
                // FIX: Target the main DatabaseSeeder class instead of the missing UsersSeeder
                Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']); 
                
                $this->info(Artisan::output());
                $this->info('DatabaseSeeder completed successfully. Check your users table now.');
            } catch (BindingResolutionException $e) {
                // This catch is unlikely to be hit now, as DatabaseSeeder always exists.
                $this->error('DatabaseSeeder failed to run. Check your seeder file for errors.');
                return Command::FAILURE;
            }
        }

        $this->info('User table reset complete.');
        return Command::SUCCESS;
    }
}
