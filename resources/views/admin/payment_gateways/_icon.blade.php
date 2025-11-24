<div class="border rounded-3 d-flex align-items-center shadow-sm" style="height:80px;width:80px;">
    @switch($type)
        @case('offline')
            <img src="{{ AppUrl::asset('images/direct-payment-logo.png') }}" alt="" width="100%" />
            @break
        @case('stripe')
            <img class="p-2" src="{{ AppUrl::asset('images/stripe-logo.svg') }}" alt="" width="100%" />
            @break
        @case('braintree')
            <img src="{{ AppUrl::asset('images/braintree-logo.png') }}" alt="" width="100%" />
            @break
        @case('paystack')
            <img src="{{ AppUrl::asset('images/paystack.png') }}" alt="" width="100%" />
            @break
        @case('paypal')
            <img src="{{ AppUrl::asset('images/paypal-logo.png') }}" alt="" width="100%" />
            @break
        @case('razorpay')
            <img src="{{ AppUrl::asset('images/razorpay.png') }}" alt="" width="100%" />
            @break
        @default
            
    @endswitch
    
</div>