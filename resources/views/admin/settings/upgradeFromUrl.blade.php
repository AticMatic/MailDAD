@extends('layouts.core.backend', [
    'menu' => 'setting',
])

@section('title', trans('messages.license'))

@section('page_header')

    <div class="page-title">
        <h1>Quick Upgrade
        </h1>
    </div>

@endsection

@section('content')

    <div class="tabbable">
        @include("admin.settings._tabs")

        <div class="tab-content">
            <div class="row">
                <div class="col-md-6">
                    @if (session('alert-error'))
                        @include('elements._notification', [
                            'level' => 'warning',
                            'title' => 'Cannot upgrade',
                            'message' => session('alert-error')
                        ])
                    @endif

                    @if (isset($failed))
                        <p class="alert alert-warning">
                            {{ trans('messages.upgrade.error.something_wrong') }}
                        </p>

                        <h3>{{ trans('messages.upgrade.title.in_progress') }}</h3>
                        <p>{!! trans('messages.upgrade.error.cannot_write') !!}</p>
                        <p>
                            <pre>{!! implode("\n", $failed) !!}</pre>
                        </p>
                        <p>
                            <a href="{{ action('Admin\SettingController@doUpgrade') }}" role="button" class="btn btn-primary me-1 upgrade-now">
                                {{ trans('messages.upgrade.button.retry') }}
                            </a>
                            <a link-confirm="{{ trans('messages.upgrade.upgrade_cancel') }}" href="{{ action('Admin\SettingController@cancelUpgrade') }}" role="button" class="btn btn-secondary btn-icon" link-method="POST">
                                {{ trans('messages.upgrade.button.cancel') }}
                            </a>
                        </p>
                    @elseif ($manager->isNewVersionAvailable())
                        <h3>{{ trans('messages.upgrade.title.upgrade_confirm') }}</h3>
                        <p>{!! trans('messages.upgrade.wording.upgrade', [ 'current' => "<code>{$manager->getCurrentVersion()}</code>", 'new' => "<code>{$manager->getNewVersion()}</code>" ]) !!}</p>
                        <p>
                            <a href="{{ action('Admin\SettingController@doUpgrade') }}" role="button" class="btn btn-primary me-1 upgrade-now">
                                {{ trans('messages.upgrade.button.upgrade_now') }}
                            </a>
                            <a link-confirm="{{ trans('messages.upgrade.upgrade_cancel') }}" href="{{ action('Admin\SettingController@cancelUpgrade') }}" role="button" class="btn btn-secondary btn-icon" link-method="POST">
                                {{ trans('messages.upgrade.button.cancel') }}
                            </a>
                        </p>
                    @elseif (!$phpversion)
                        <h3>{{ trans('messages.upgrade.title.current') }}</h3>
                        <p>{!! trans('messages.upgrade.wording.upload', [ 'current' => "<code>{$manager->getCurrentVersion()}</code>" ]) !!}</p>

                        @include('elements._notification', [
                            'level' => 'warning',
                            'title' => trans('messages.requirement.php_version.not_supuported.title'),
                            'message' => trans('messages.requirement.php_version.not_supuported.description', ['current' => '<strong>'.PHP_VERSION.'</strong>', 'required' => '<strong>'.config('custom.php_recommended').'</strong>']),
                        ])
                    @else
                        @if ($license && ($license->isExpired() || $license->isInactive()))
                            @php
                                $admin = Auth::user()->admin;
                                $spportedUntil = $license->getSupportedUntil($admin->getTimezone());

                                if (config('custom.japan')) {
                                    $entitlementLink = '#';
                                } else {
                                    $entitlementLink = 'https://codecanyon.net/item/acelle-email-marketing-web-application/17796082';
                                }

                                $style = 'alert-danger';

                                if ($license->isExpired()) {
                                    $title = trans('messages.support.expired.explanation', [
                                        'expr' =>  $admin->formatDateTime($spportedUntil, 'datetime_full_with_timezone'),
                                        'diffs' => $spportedUntil->diffForHumans()]
                                    );
                                } elseif ($license->isInactive()) {
                                    $title = trans('messages.license.error.invalid');
                                }
                            @endphp

                            <div class="sub-section">
                                <h3>{{ trans('messages.license.your_license') }}</h3>
                                <p>{{ $title }}</p>
                                <div class="alert {{ $style }}" style="display: flex; flex-direction: row; align-items: center;">
                                    <div style="display: flex; flex-direction: row; align-items: center;">
                                        <p style="padding-left: 5px;padding-right: 40px">{{ $license->getLicenseNumber() }}{!! $license->isExpired() ? ' | <a href="'.$entitlementLink.'"><strong style="text-decoration: underline;">'.trans('messages.support.expired.note').'</strong></a>' : '' !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h3>{{ trans('messages.upgrade.title.current') }}</h3>
                        <p>{!! trans('messages.upgrade.wording.upload', [ 'current' => "<code>{$manager->getCurrentVersion()}</code>" ]) !!}</p>
                        <p>{{ trans('messages.upgrade.notice') }}</p>
                        <ul>
                            <li><code>post_max_size</code> <strong>{{ ini_get('post_max_size') }}</strong></li>
                            <li><code>upload_max_filesize</code> <strong>{{ ini_get('upload_max_filesize') }}</strong></li>
                        </ul>
                        <form action="{{ action('Admin\SettingController@upgradeFromUrl') }}" method="POST" class="form-validate-jqueryz">
                            {{ csrf_field() }}

                            <p>Patch URL</p>
                            <input type="text" name="url" size="80">
                            <input type="submit" name="Upgrade">

                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection
