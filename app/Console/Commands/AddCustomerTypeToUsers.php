<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AddCustomerTypeToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:add-customer-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add customer user_type to users where user_type is null and assign customer role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting customer type migration...');

        // Ensure customer role exists
        $customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['guard_name' => 'web']
        );
        $this->info('Customer role ensured: ' . $customerRole->name);

        // Get all users with null user_type
        $usersToUpdate = User::whereNull('user_type')->get();
        $count = $usersToUpdate->count();

        if ($count === 0) {
            $this->info('No users found with null user_type. Nothing to update.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} user(s) with null user_type.");

        if (!$this->confirm('Do you want to update these users?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;
        $roleAssigned = 0;

        foreach ($usersToUpdate as $user) {
            // Update user_type to 'customer'
            $user->user_type = 'customer';
            $user->save();

            // Assign customer role if not already assigned
            if (!$user->hasRole('customer')) {
                $user->assignRole($customerRole);
                $roleAssigned++;
            }

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully updated {$updated} user(s) with user_type='customer'.");
        $this->info("Assigned customer role to {$roleAssigned} user(s).");

        return Command::SUCCESS;
    }
}

