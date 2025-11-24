@if ($paymentGateways->count() > 0)
    <table class="table table-box pml-table mt-2"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($paymentGateways as $key => $paymentGateway)
            <tr class="position-relative">
                <td width="5%" class="pe-3">
                    @include('admin.payment_gateways._icon', [
                        'type' => $paymentGateway->type,
                    ])
                </td>
                <td>
                    <h5 class="no-margin text-bold kq_search">
                        {{ $paymentGateway->name }}
                    </h5>
                    <span class="text-muted">{{ trans('messages.payment_gateway.name') }}</span>
                </td>
                <td>
                    <span class="no-margin stat-num">{{ $paymentGateway->description }}</span>
                    <br>
                    <span class="text-muted2">{{ trans('messages.payment_gateway.description') }}</span>
                </td>
                <td>
                    <span class="no-margin stat-num">
                        {{ Auth::user()->admin->formatDateTime($paymentGateway->created_at, 'datetime_full') }}
                    </span>
                    <br>
                    <span class="text-muted2">{{ trans('messages.created_at') }}</span>
                </td>
                <td>
                    <span class="label label-flat bg-{{ $paymentGateway->status }}">{{ trans('messages.payment_gateway.status.' . $paymentGateway->status) }}</span>
                </td>
                <td class="text-end text-nowrap pe-0" width="5%">
                    <div class="list-actions">
                        <a href="{{ action('Admin\PaymentGatewayController@edit', $paymentGateway->uid) }}" role="button" class="btn btn-secondary btn-icon"> <span class="material-symbols-rounded">edit</span> {{ trans('messages.edit') }}</a>
                        <div class="btn-group">
                            <button role="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (Auth::user()->admin->can('enable', $paymentGateway))
                                    <li>
                                        <a class="dropdown-item list-action-single"
                                            link-method="POST"
                                            link-confirm="{{ trans('messages.payment_gateway.enable.confirm') }}"
                                            href="{{ action('Admin\PaymentGatewayController@enable', $paymentGateway->uid) }}"
                                            title="{{ trans('messages.payment_gateway.enable') }}" class=""
                                        >
                                            <span class="material-symbols-rounded">check</span> {{ trans('messages.payment_gateway.enable') }}
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::user()->admin->can('disable', $paymentGateway))
                                    <li>
                                        <a class="dropdown-item list-action-single"
                                            link-method="POST"
                                            link-confirm="{{ trans('messages.payment_gateway.disable.confirm') }}"
                                            href="{{ action('Admin\PaymentGatewayController@disable', $paymentGateway->uid) }}"
                                            title="{{ trans('messages.payment_gateway.disable') }}" class=""
                                        >
                                            <span class="material-symbols-rounded">block</span> {{ trans('messages.payment_gateway.disable') }}
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item list-action-single"
                                        link-method="POST"
                                        link-confirm="{{ trans('messages.payment_gateway.delete.confirm') }}"
                                        href="{{ action('Admin\PaymentGatewayController@delete', $paymentGateway->uid) }}"
                                        title="{{ trans('messages.payment_gateway.delete') }}" class=""
                                    >
                                        <span class="material-symbols-rounded">delete</span> {{ trans('messages.payment_gateway.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $paymentGateways])
    

    <script>
        var PlanList = {
			copyPopup: null,

			getCopyPopup: function() {
				if (this.copyPopup === null) {
					this.copyPopup = new Popup();
				}

				return this.copyPopup;
			}
		}

        $(function() {
            $('.copy-plan-link').on('click', function(e) {
                e.preventDefault();			
                var url = $(this).attr('href');

                PlanList.getCopyPopup().load({
                    url: url
                });
            });

            $('.cant_show').click(function(e) {
                e.preventDefault();

                var confirm = `{{ trans('messages.plan.cant_show') }}`;
                var dialog = new Dialog('alert', {
                    message: confirm
                })
            });

            $('.enable-plan').click(function(e) {
                e.preventDefault();

                var confirm = `{{ trans('messages.plan.enable_and_visible.confirm') }}`;
                var href_yes = $(this).attr('href_yes');
                var href_no = $(this).attr('href_no');

                var dialog = new Dialog('yesno', {
                    message: confirm,
                    no: function(dialog) {
                        $.ajax({
                            url: href_no,
                            method: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                            },
                            statusCode: {
                                // validate error
                                400: function (res) {
                                    alert('Something went wrong!');
                                }
                            },
                            success: function (response) {
                                // notify
                                notify({
        type: 'success',
        title: '{!! trans('messages.notify.success') !!}',
        message: response.message
    });
                            }
                        });
                    },
                    yes: function(dialog) {                    
                        $.ajax({
                            url: href_yes,
                            method: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                            },
                            statusCode: {
                                // validate error
                                400: function (res) {
                                    alert('Something went wrong!');
                                }
                            },
                            success: function (response) {
                                // notify
                                notify({
        type: 'success',
        title: '{!! trans('messages.notify.success') !!}',
        message: response.message
    });
                            }
                        });
                    },
                });
            });
        });
    </script>
@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <i class="material-symbols-rounded">credit_card</i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="material-symbols-rounded">credit_card</i>
        <span class="line-1">
            {{ trans('messages.payment_gateway.empty') }}
        </span>
    </div>
@endif
