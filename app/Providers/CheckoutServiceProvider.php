<?php

namespace Acelle\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Acelle\Library\BillingManager;
use Acelle\Library\Facades\Billing;
use Acelle\Cashier\Services\StripePaymentGateway;
use Acelle\Cashier\Services\OfflinePaymentGateway;
use Acelle\Cashier\Services\BraintreePaymentGateway;
use Acelle\Cashier\Services\CoinpaymentsPaymentGateway;
use Acelle\Cashier\Services\PaystackPaymentGateway;
use Acelle\Cashier\Services\PaypalPaymentGateway;
use Acelle\Cashier\Services\RazorpayPaymentGateway;

class CheckoutServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register built-in services
        // Notice that the closure passed to the register() method is not actually executed during boot
        // It is to improve performance and to avoid executing DB queries in service providers
        Billing::register(
            OfflinePaymentGateway::TYPE,
            trans('cashier::messages.offline'),
            trans('cashier::messages.offline.description')
        );

        Billing::register(
            StripePaymentGateway::TYPE,
            trans('cashier::messages.stripe'),
            trans('cashier::messages.stripe.description')
        );

        Billing::register(
            BraintreePaymentGateway::TYPE,
            trans('cashier::messages.braintree'),
            trans('cashier::messages.braintree.description')
        );

        Billing::register(
            PaystackPaymentGateway::TYPE,
            trans('cashier::messages.paystack'),
            trans('cashier::messages.paystack.description')
        );

        Billing::register(
            PaypalPaymentGateway::TYPE,
            trans('cashier::messages.paypal'),
            trans('cashier::messages.paypal.description')
        );

        Billing::register(
            RazorpayPaymentGateway::TYPE,
            trans('cashier::messages.razorpay'),
            trans('cashier::messages.razorpay.description')
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BillingManager::class, function ($app) {
            return new BillingManager();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [BillingManager::class];
    }
}
