<ul class="nav nav-tabs nav-tabs-top nav-underline">
	<li class="nav-item text-semibold">
		<a class="nav-link {{ $tab == "recurring" ? "active" : "" }}"
			href="{{ action('Admin\PlanVerificationController@index') }}"
		>
			<span class="material-symbols-rounded">event_repeat</span> 
			{{ trans('messages.email_verification_plan.recurring') }}
		</a>
	</li>
	<li class="nav-item text-semibold">
		<a class="nav-link {{ $tab == "one_time_pay" ? "active" : "" }}"
			href="{{ action('Admin\EmailVerificationPlanController@index') }}"
		>
			<span class="material-symbols-rounded">paid</span> 
			{{ trans('messages.email_verification_plan.one_time_pay') }}
		</a>
	</li>
</ul>
