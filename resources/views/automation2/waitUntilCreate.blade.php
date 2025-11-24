@extends('layouts.popup.small')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form data-control="WaitActionForm" id="action-select" action="{{ action('Automation2Controller@waitUntilSave', ['uid' => $automation->uid]) }}" method="POST" class="form-validate-jqueryz">
                {{ csrf_field() }}
                
                @include('automation2.action.waitUntil')

                <button class="btn btn-secondary select-action-confirm mt-2">
                        {{ trans('messages.automation.trigger.select_confirm') }}
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectActionSubmit(url, data) {
            // show loading effect
            automationPopup.loading();

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
            }).always(function(response) {
                var newE = new ElementWaitUntil({title: response.title, options: response.options});

                MyAutomation.addToTree(newE);

                newE.validate();
                
                // save tree
                saveData(function() {
                    // hide popup
                    automationPopup.hide();

                    doSelectTreeElement(newE);
                    
                    notify({
                        type: 'success',
                        title: '{!! trans('messages.notify.success') !!}',
                        message: response.message
                    });
                });
            });
        }

        $(() => {
            $('[data-control="WaitActionForm"]').submit(function(e) {
                e.preventDefault();

                var url = $(this).attr('action');
                var data = $(this).serialize();

                // validation
                if ($(this)[0].reportValidity() == false) {
                    return false;
                    e.preventDefault();
                }
                
                // submit form
                selectActionSubmit(url, data);
            });
        })
    </script>
@endsection
