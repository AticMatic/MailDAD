<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $convertPayments = [
            [
                'name' => 'Offline',
                'type' => 'offline',
                'description' => trans('cashier::messages.offline.description'),
                'fields' => [
                    'payment_instruction' => \Acelle\Model\Setting::get('cashier.offline.payment_instruction'),
                ],
            ],

            [
                'name' => 'Stripe',
                'type' => 'stripe',
                'description' => trans('cashier::messages.stripe.description'),
                'fields' => [
                    'publishable_key' => \Acelle\Model\Setting::get('cashier.stripe.publishable_key'),
                    'secret_key' => \Acelle\Model\Setting::get('cashier.stripe.secret_key'),
                ],
            ],

            [
                'name' => 'Braintree',
                'type' => 'braintree',
                'description' => trans('cashier::messages.braintree.description'),
                'fields' => [
                    'environment' => \Acelle\Model\Setting::get('cashier.braintree.environment'),
                    'merchant_id' => \Acelle\Model\Setting::get('cashier.braintree.merchant_id'),
                    'public_key' => \Acelle\Model\Setting::get('cashier.braintree.public_key'),
                    'private_key' => \Acelle\Model\Setting::get('cashier.braintree.private_key'),
                ],
            ],

            [
                'name' => 'Paystack',
                'type' => 'paystack',
                'description' => trans('cashier::messages.paystack.description'),
                'fields' => [
                    'public_key' => \Acelle\Model\Setting::get('cashier.paystack.public_key'),
                    'secret_key' => \Acelle\Model\Setting::get('cashier.paystack.secret_key'),
                ],
            ],

            [
                'name' => 'Paypal',
                'type' => 'paypal',
                'description' => trans('cashier::messages.paypal.description'),
                'fields' => [
                    'environment' => \Acelle\Model\Setting::get('cashier.paypal.environment'),
                    'client_id' => \Acelle\Model\Setting::get('cashier.paypal.client_id'),
                    'secret' => \Acelle\Model\Setting::get('cashier.paypal.secret'),
                ],
            ],

            [
                'name' => 'Razorpay',
                'type' => 'razorpay',
                'description' => trans('cashier::messages.razorpay.description'),
                'fields' => [
                    'key_id' => \Acelle\Model\Setting::get('cashier.razorpay.key_id'),
                    'key_secret' => \Acelle\Model\Setting::get('cashier.razorpay.key_secret'),
                ],
            ],
        ];

        $enabledPayments = json_decode(\Acelle\Model\Setting::get('gateways'), true);

        // Migrate all old data to new version2
        \Acelle\Model\PaymentGateway::query()->delete(); // Delete all existing payment gateways
        foreach ($convertPayments as $convertPayment) {
            $firstFieldValue = reset($convertPayment['fields']); // Get the first item value
            if ($firstFieldValue != '') {
                // New gateway
                $paymentGateway = \Acelle\Model\PaymentGateway::newDefault($convertPayment['type']);
                $paymentGateway->name = $convertPayment['name'];
                $paymentGateway->description = $convertPayment['description'];
                $paymentGateway->status = in_array($convertPayment['type'], $enabledPayments) ? \Acelle\Model\PaymentGateway::STATUS_ACTIVE : \Acelle\Model\PaymentGateway::STATUS_INACTIVE;

                $paymentGateway->gatewayData = json_encode($convertPayment['fields']);

                $paymentGateway->save();

                // Find all customer that have payment method
                $customers = \Acelle\Model\Customer::where('payment_method', 'LIKE', '%'.$paymentGateway->type.'%')
                    ->whereNotNull('auto_billing_data')
                    ->get();
                
                // update payment method for customers
                foreach ($customers as $customer) {
                    $oldAutoBillingData = json_decode($customer->auto_billing_data, true)['data'];

                    switch($convertPayment['type']) {
                        case 'stripe':
                            // Payment method
                            $autobillingData = json_encode([
                                'payment_method_id' => $oldAutoBillingData['payment_method_id'],
                                'customer_id' => $oldAutoBillingData['customer_id'],
                                'card_type' => 'Card',
                                'last_4' => '***',
                            ]);
                            $customer->paymentMethods()->create(
                                [
                                    'payment_gateway_id' => $paymentGateway->id,
                                    'autobilling_data' => $autobillingData,
                                    'can_auto_charge' => true,
                                ]
                            );
                            break;
                        case 'paystack':
                            // Payment method
                            $autobillingData = json_encode([
                                'authorization_code' => $oldAutoBillingData['last_transaction']['data']['authorization']['authorization_code'],
                                'email' => $oldAutoBillingData['last_transaction']['data']['customer']['email'],
                                'last_4' => $oldAutoBillingData['last_transaction']['data']['authorization']['last4'],
                                'card_type' => 'Card',
                            ]);
                            $customer->paymentMethods()->create(
                                [
                                    'payment_gateway_id' => $paymentGateway->id,
                                    'autobilling_data' => $autobillingData,
                                    'can_auto_charge' => true,
                                ]
                            );
                            break;
                        case 'braintree':
                            // Payment method
                            $autobillingData = json_encode([
                                'payment_method_token' => $oldAutoBillingData['paymentMethodToken'],
                                'last_4' => $oldAutoBillingData['card_last4'],
                                'card_type' => $oldAutoBillingData['card_type'],
                            ]);
                            $customer->paymentMethods()->create(
                                [
                                    'payment_gateway_id' => $paymentGateway->id,
                                    'autobilling_data' => $autobillingData,
                                    'can_auto_charge' => true,
                                ]
                            );
                            break;
                    }
                        
                }
            }
        }

        // cleanup old settings
        \Acelle\Model\Setting::where('name', 'LIKE', 'cashier.%')->delete();
        \Acelle\Model\Setting::where('name', 'gateways')->delete();
    }
}
