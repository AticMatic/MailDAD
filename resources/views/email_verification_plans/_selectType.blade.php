<ul class="nav nav-tabs nav-tabs-top nav-underline">
	<li class="nav-item text-semibold">
		<a data-action="select-type" class="nav-link {{ $tab == "recurring" ? "active" : "" }}"
			href="{{ action("PlanVerificationController@select") }}"
		>
			<span class="material-symbols-rounded">event_repeat</span> 
			{{ trans('messages.email_verification_plan.recurring') }}
		</a>
	</li>
	<li class="nav-item text-semibold">
		<a data-action="select-type" class="nav-link {{ $tab == "one_time_pay" ? "active" : "" }}"
			href="{{ action("EmailVerificationPlanController@select") }}"
		>
			<span class="material-symbols-rounded">paid</span> 
			{{ trans('messages.email_verification_plan.one_time_pay') }}
		</a>
	</li>
</ul>

<script>
	$(() => {
		var selectPlanType = new SelectPlanType({
			buttons: $('[data-action="select-type"]'),
		});
	});

	var SelectPlanType = class {
		constructor(options) {
			var _this = this;
			this.buttons = options.buttons;

			this.buttons.on('click', function(e) {
				e.preventDefault();

				var url = $(this).attr('href');

				_this.selectType(url);
			});
		}

		selectType(url) {
			window.selectPlanPopup.popup.load(url);
		}
	}
</script>
