<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Model\TrackingDomain;
use Acelle\Model\Notification;

class CaddyRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caddy:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all tracking domains and write config files';

    private function logger()
    {
        return applog('caddy');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->withAutoSslTrackingDomains(function ($customer, $domains) {
            $this->logger()->info('Checking customer '.$customer->name);
            foreach ($domains as $domain) {
                $this->logger()->info('+ Checking domain '.$domain->name);
                $this->createCaddyConfigFile($domain->name);
            }
        });

        return 0;
    }

    private function createCaddyConfigFile($name)
    {
        $path = join_paths(config('caddy.config_dir'), "$name");
        $proxy = config('caddy.reverse_proxy');
        $adminEmail = config('caddy.admin_email_address');
        $config = <<<END
{$name} {
       tls {$adminEmail}
       reverse_proxy {$proxy}
}
END;
        if (!file_exists($path)) {
            $this->logger()->info('+ File does not exist, creating '.$name);
            file_put_contents($path, $config);
        }
    }

    private function withAutoSslTrackingDomains($callback)
    {
        $customers = \Acelle\Model\Customer::all();
        foreach ($customers as $customer) {
            $this->logger()->info("Registering tracking domains for {$customer->name}");
            \Acelle\Model\Notification::recordIfFails(function () use ($customer, $callback) {
                $customer->setUserDbConnection();
                $domains = $customer->local()->trackingDomains()->autoSsl()->get();
                $callback($customer, $domains);
            }, 'Caddy failed', function ($ex) use ($customer) { echo "Customer failed {$customer->name}: {$ex->getMessage()} "; }, "Customer {$customer->name} - ");
        }
    }
}
