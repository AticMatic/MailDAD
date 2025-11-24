<?php

namespace Acelle\Library\Contracts;

use Acelle\Model\Invoice;
use Acelle\Model\Transaction;
use Acelle\Model\PaymentMethod;

interface PaymentGatewayInterface
{
    // Some payment gateway plugin has its own page for handling the payment process
    // For example, Stripe will redirect users to the check pages in which users can enter their credit/debit card information
    public function getCheckoutUrl(Invoice $invoice, string $paymentGatewayId): string;

    // Check if a payment gateway supports auto billing
    // i.e. Stripe allows users to enter their credit/debit cards to the Stripe service
    // which is uniquely identified by a Token
    // The application can stores the token and use it to automatically charge the related card
    public function supportsAutoBilling(): bool;

    // Charge an invoice in the background
    // This method is executed in the background
    public function autoCharge(Invoice $invoice, PaymentMethod $paymentMethod); // dành cho cronjob của core gọi

    //
    public function allowManualReviewingOfTransaction(): bool;
    public function getMinimumChargeAmount($currency);

    // verify pending transaction
    public function verify(Transaction $transaction);

    // get method title
    public function getMethodTitle($billingData);

    // get method info
    public function getMethodInfo($billingData);
}
