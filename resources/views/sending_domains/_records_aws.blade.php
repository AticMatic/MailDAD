<table class="table table-trans tbody-white" class="table-layout:fixed">
    <thead>
        <tr>
            <th class="trans-upcase text-semibold">{{ trans('messages.type') }}</th>
            <th class="trans-upcase text-semibold">{{ trans('messages.host') }}</th>
            <th class="trans-upcase text-semibold">{{ trans('messages.value') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @if (!is_null($identity))
            <tr class="position-relative">
                <td width="1%" class="list-check-col">
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-ended square-tag">{{ $identity['type'] }}</span>
                    </span>
                </td>
                <td width="20%">
                    <span>{{ $identity['name'] }}</span>
                </td>
                <td><span>{{ $identity['value'] }}</span></td>
                <td class="text-end" width="1%">
                    @if ($domain->isIdentityVerified())
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-active">{{ trans('messages.sending_domain.verified') }}</span>
                        </span>
                    @else
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain.pending') }}</span>
                        </span>
                    @endif
                </td>
            </tr>
        @endif

        @if (!is_null($dmarc))
            <tr class="position-relative">
                <td width="1%" class="list-check-col">
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-ended square-tag">{{ $dmarc['type'] }}</span>
                    </span>
                </td>
                <td width="20%">
                    <span>{{ $dmarc['name'] }}</span>
                </td>
                <td><span>{{ $dmarc['value'] }}</span></td>
                <td class="text-end" width="1%">
                    @if ($domain->isDmarcVerified())
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-active">{{ trans('messages.sending_domain.verified') }}</span>
                        </span>
                    @else
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain.pending') }}</span>
                        </span>
                    @endif
                </td>
            </tr>
        @endif

        @foreach ($dkims as $dkim)
            <!-- Convention: DKIM record with null 'value' means 'CANNOT RETRIEVE DNS RECORD, THE API DOES NOT RETURN THEM WHEN CREATING' (see ElasticEmail addDomain()) -->
            @if (!array_key_exists('value', $dkim))
                <tr>
                    <td>
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-ended square-tag">TXT</span>
                        </span>
                    </td>
                    <td>
                        <span>{{ $dkim['name'] }}</span>
                    </td>
                    <td width="60%" style="word-wrap:break-word;word-break:break-all;"><code>{{ trans('messages.sending_domain.dkim.not_available', ['name' => $domain->sendingServer->mapType()->getServiceName()]) }}</code></td>
                    <td class="text-end">
                        @if ($domain->isDkimVerified())
                            <span class="text-muted2 list-status pull-left">
                                <span class="label label-flat bg-active">{{ trans('messages.sending_domain.verified') }}</span>
                            </span>
                        @else
                            <span class="text-muted2 list-status pull-left">
                                <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain.pending') }}</span>
                            </span>
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td>
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-ended square-tag">{{ $dkim['type'] }}</span>
                        </span>
                    </td>
                    <td>
                        <span>{{ $dkim['name'] }}</span>
                    </td>
                    <td width="60%" style="word-wrap:break-word;word-break:break-all;"><span>{{ $dkim['value'] }}</span></td>
                    <td class="text-end">
                        @if ($domain->isDkimVerified())
                            <span class="text-muted2 list-status pull-left">
                                <span class="label label-flat bg-active">{{ trans('messages.sending_domain.verified') }}</span>
                            </span>
                        @else
                            <span class="text-muted2 list-status pull-left">
                                <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain.pending') }}</span>
                            </span>
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach

        @if (!is_null($spf))
            @foreach ($spf as $r)
            <tr>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-ended square-tag">{{ $r['type'] }}</span>
                    </span>
                </td>
                <td>
                    <span>{{ $r['name'] }}</span>
                </td>
                <td><span>{{ $r['value'] }}</span></td>
                <td class="text-end">
                    @if ($domain->isSpfVerified())
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-active">{{ trans('messages.sending_domain.verified') }}</span>
                        </span>
                    @else
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-flat bg-inactive">{{ trans('messages.sending_domain.pending') }}</span>
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
