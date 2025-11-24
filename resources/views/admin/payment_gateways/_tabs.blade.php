<ul class="nav nav-tabs nav-tabs-top nav-underline">
	<li class="nav-item text-semibold">
		<a class="nav-link {{ $tab == "gateways" ? "active" : "" }}" href="{{ action('Admin\PaymentGatewayController@index') }}">
		<span class="material-symbols-rounded">payments</span>  {{ trans('messages.payment_gateways') }}</a>
	</li>
	<li class="nav-item text-semibold">
		<a class="nav-link {{ $tab == "settings" ? "active" : "" }}" href="{{ action('Admin\PaymentGatewayController@settings') }}">
		<span class="material-symbols-rounded">tune</span>  {{ trans('messages.payment_gateway.settings') }}</a>
	</li>
</ul>
