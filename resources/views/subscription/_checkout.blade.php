@if ($invoice->isNew() && !$invoice->getLastAndItIsPendingTransaction())
    <div class="">
        <div class="mt-4">
            <div class="alert alert-info bg-no-order tos_link mb-0">
                @if (Acelle\Model\Setting::get('terms_of_service.enabled') == 'no' && Acelle\Model\Setting::get('terms_of_service.content')) 
                    {!! trans('messages.payment.agree_service_intro.term_link', [
                        'link' => 'asas',
                    ]) !!}
                @else
                    {!! trans('messages.payment.agree_service_intro') !!}
                @endif
            </div>

            @if (request()->payment_gateway_id)
                <form id="checkoutForm" class="mt-4" action="{{ action('SubscriptionController@checkout', [
                    'invoice_uid' => $invoice->uid,
                ]) }}"
                    method="POST">
                    {{ csrf_field() }}

                    <input type="hidden" name="payment_gateway_id" value="{{ request()->payment_gateway_id }}" />

                    <input type="submit" name="new_payment"
                        class="btn btn-primary py-2 fs-6 px-4 text-center rounded-3 shadow" style="width:100%"
                        value="{{ $invoice->isFree() ? trans('messages.subscription.get_started') : trans('messages.subscription.checkout') }}"
                    >
                </form>
            @endif

            @if (request()->payment_method_id)
                <form id="checkoutForm" class="mt-4" action="{{ action('SubscriptionController@autoCharge', [
                    'invoice_uid' => $invoice->uid,
                    'payment_method_id' => request()->payment_method_id,
                ]) }}"
                    method="POST">
                    {{ csrf_field() }}

                    <input type="submit" name="new_payment"
                        class="btn btn-primary py-2 fs-6 px-4 text-center rounded-3 shadow" style="width:100%"
                        value="{{ $invoice->isFree() ? trans('messages.subscription.get_started') : trans('messages.subscription.checkout') }}"
                    >
                </form>
            @endif
        </div>
    </div>

    @if ($invoice->type == \Acelle\Model\InvoiceChangePlan::TYPE_CHANGE_PLAN)
        <div class="text-center mt-4">
            <a class="btn btn-link text-center rounded-3"
                style="width:100%"
                link-method="POST"
                link-confirm="{{ trans('messages.invoice.cancel.confirm') }}"
                href="{{ action('SubscriptionController@cancelInvoice', [
                    'invoice_uid' => $bill['invoice_uid'],
                ]) }}"
            >
                {{ trans('messages.invoice.change_plan.cancel') }}
            </a>
        </div>
    @endif

    <script>
        $(function() {
            $('.tos_link a').on('click', function(e) {
                e.preventDefault();

                var popup = new Popup({
                    url: '{{ action('AppController@termsOfService') }}'
                });
                popup.load();
            });
        });
    </script>
@endif