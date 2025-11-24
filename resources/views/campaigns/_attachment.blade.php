<div class="image_upload_div">
  <form action="{{ action('CampaignController@uploadAttachment', $campaign->uid) }}" class="dropzone">
     {{ csrf_field() }}
     <div class="fallback">
        <input name="files[]" type="file" multiple />
     </div>
  </form>
</div>
<div class="attachments_pnl">
    @if($campaign->attachments()->count())
        <h5 class="mt-4 mb-3">{{ trans('messages.campaign.attached_files') }}</h5>
        <div class="row">
            <div class="col-md-12">
                <ul class="key-value-list list-small">
                    @foreach ($campaign->attachments as $attachment)
                        <li class="flex align-items-center">
                            <div class="icon">
                                <span class="material-symbols-rounded me-2">attach_email</span>
                            </div>
                            <div class="content mr-auto">
                                <label>
                                    {{ $attachment->name }}
                                </label>
                                <div class="value">
                                    {{ trans('messages.campaign.attachment.file_size_is', ['size' => formatSizeUnits($attachment->size)]) }}
                                </div>
                            </div>
                            <div class="action">
                                <a                                
                                    href="{{ action('CampaignController@downloadAttachment', [
                                        'uid' => $campaign->uid,
                                        'attachment_uid' => $attachment->uid,
                                    ]) }}"
                                    class="btn btn-link px-2"
                                    title="{{ trans('messages.campaign.attachment.download') }}"
                                >
                                    <span class="material-symbols-rounded fs-6">file_download</span>
                                </a>
                                <a                                
                                    href="{{ action('CampaignController@removeAttachment', [
                                        'uid' => $campaign->uid,
                                        'attachment_uid' => $attachment->uid,
                                    ]) }}"
                                    class="btn btn-link remove-attachment px-2"
                                    title="{{ trans('messages.campaign.attachment.remove') }}"
                                >
                                    <span class="material-symbols-rounded  fs-6 text-danger">delete</span>
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>

<script>
Dropzone.autoDiscover = false;

$(".dropzone").dropzone({
    uploadMultiple: true,
    success: function() {
        reloadList();
    }
});

function reloadList() {
    $.ajax({
        method: 'GET',
        url: '',
    })
    .done(function(msg) {
        $('.attachments_pnl').html($(msg).find('.attachments_pnl').html());
    });
}

$(document).on('click', '.remove-attachment', function(e) {
    e.preventDefault();

    var url = $(this).attr('href');

    $.ajax({
        method: 'POST',
        url: url,
        data: {
            _token: CSRF_TOKEN
        }
    })
    .done(function(response) {
        reloadList();
        notify({
            type: 'success',
            title: '{!! trans('messages.notify.success') !!}',
            message: response.message
        });
    });
});
</script>
