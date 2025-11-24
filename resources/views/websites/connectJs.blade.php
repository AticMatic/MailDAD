@include('forms.frontend.popupJs')

window.onload = function() {
    @foreach($website->connectedForms()->published()->get() as $form)
        @include('forms.frontend.popup', [
            'form' => $form,
        ])
    @endforeach
};