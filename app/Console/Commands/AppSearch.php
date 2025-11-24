<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Model\Campaign;
use Acelle\Model\Customer;
use Acelle\Model\Automation2;

class AppSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:search {idOrUid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for Campaign, Customer, or Automation2 by ID or UID and print related info';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idOrUid = $this->argument('idOrUid');

        // --- Search Customer by ID or UID ---
        $customer = Customer::where('id', $idOrUid)
            ->orWhere('uid', $idOrUid)
            ->first();

        if ($customer) {
            $this->info("✅ Found Customer:");
            $this->line("Name: " . $customer->name);
            $this->line("ID: " . $customer->id);
            $this->line("Email: " . optional($customer->users()->first())->email);
            return Command::SUCCESS;
        }

        // --- Search Campaign by ID or UID ---
        $campaign = Campaign::where('id', $idOrUid)
            ->orWhere('uid', $idOrUid)
            ->first();

        if ($campaign) {
            $this->info("✅ Found Campaign:");
            $this->line("Name: " . $campaign->name);
            $this->line("ID: " . $campaign->id);

            if ($campaign->customer) {
                $this->info("🔗 Related Customer:");
                $this->line("Name: " . $campaign->customer->name);
                $this->line("Email: " . optional($campaign->customer->users()->first())->email);
            }
            return Command::SUCCESS;
        }

        // --- Search Automation2 by ID or UID ---
        $automation = Automation2::where('id', $idOrUid)
            ->orWhere('uid', $idOrUid)
            ->first();

        if ($automation) {
            $this->info("✅ Found Automation2:");
            $this->line("Name: " . $automation->name);
            $this->line("ID: " . $automation->id);

            if ($automation->customer) {
                $this->info("🔗 Related Customer:");
                $this->line("Name: " . $automation->customer->name);
                $this->line("Email: " . optional($automation->customer->users()->first())->email);
            }
            return Command::SUCCESS;
        }

        // --- Not Found ---
        $this->error("❌ No matching record found for '{$idOrUid}'");
        return Command::FAILURE;
    }
}
