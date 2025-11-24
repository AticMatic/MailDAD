
var popup_{{ $form->uid }} = new AFormPopup({
    url: '{{ action('FormController@frontendContent', [
        'uid' => $form->uid,
    ]) }}',
    overlayOpacity: '{{ $form->getMetadata('overlay_opacity') ? ($form->getMetadata('overlay_opacity')/100) : '0.2' }}'
});

popup_{{ $form->uid }}.init();

@if ($form->getMetadata('display') == 'click')
    var elementFound = false;
    if (document.getElementById('{{ $form->getMetadata('element_id') }}')) {
        document.getElementById('{{ $form->getMetadata('element_id') }}').addEventListener("click", function(event) {
            console.log('{{ $form->getMetadata('element_id') }} clicked');
            popup_{{ $form->uid }}.show();
        });
        elementFound = true;
    }
    if (document.querySelectorAll('{!! $form->getMetadata('element_id') !!}').length) {
        document.querySelectorAll('{!! $form->getMetadata('element_id') !!}').forEach(ele => {
            ele.addEventListener("click", function(event) {
                popup_{{ $form->uid }}.show();
            });
        });
        elementFound = true;
    }

    if (!elementFound) {
        {{-- alert('[{!! get_app_name() !!}] Popup was configured to load when clicking on element with ID #{!! $form->getMetadata('element_id') !!}, but the element was not found!'); --}}
    }
@elseif ($form->getMetadata('display') == 'wait')
    setTimeout(function() {
        popup_{{ $form->uid }}.show();
    }, {{ $form->getMetadata('wait_time')*1000 }});

@elseif ($form->getMetadata('display') == 'first_visit')
    popup_{{ $form->uid }}.loadOneTime();
@elseif ($form->getMetadata('display') == 'on_exit_intent')
    document.addEventListener('mouseleave', function(event) {
        if (event.clientY < 0) {
            showPopup();
        }
    });

    function showPopup() {
        popup_{{ $form->uid }}.show();
    }

    function closePopup() {
        popup_{{ $form->uid }}.hide();
    }

    window.addEventListener('beforeunload', function (event) {
        event.preventDefault();
        event.returnValue = '';
    });
@else
    setTimeout(function() {
        popup_{{ $form->uid }}.show();
    }, 500);
@endif

