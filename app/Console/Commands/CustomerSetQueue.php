<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Model\User;
use Acelle\Model\Customer;

class CustomerSetQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan customer:set-queue user@example.com custom01
     */
    protected $signature = 'customer:set-queue {email} {queueName}';

    protected $description = 'Assign a custom queue name to a customer based on user email';

    public function handle()
    {
        $email = $this->argument('email');
        $queueName = $this->argument('queueName');

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return Command::FAILURE;
        }

        // Get related customer
        $customer = $user->customer;

        if (!$customer) {
            $this->error("User {$email} has no associated customer record.");
            return Command::FAILURE;
        }

        if ($customer->custom_queue_name === $queueName) {
            $this->warn("Customer {$customer->id} already has queue '{$queueName}'. No update needed.");
        } else {
            $customer->custom_queue_name = $queueName;
            $customer->save();
            $this->info("Updated customer {$customer->id} queue to '{$queueName}'.");
        }

        // Count total customers with this queue name
        $count = Customer::where('custom_queue_name', $queueName)->count();
        $this->info("Total customers with queue '{$queueName}': {$count}");

        return Command::SUCCESS;
    }
}
