<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;
use Acelle\Model\HttpRequest;
use Acelle\Model\HttpConfig;

class HttpConfigController extends Controller
{
    public function test(Request $request, $uid)
    {
        $httpConfig = HttpConfig::findByUid($uid);

        if ($request->isMethod('post')) {
            // just test
            $httpRequest = $httpConfig->test([], $request->webhook);

            return view('admin.http_configs.testResult', [
                'webhook' => $httpConfig,
                'httpRequestLog' => $httpRequest->httpRequestLogs()->latest()->first(),
            ]);
        }

        return view('admin.http_configs.test', [
            'httpConfig' => $httpConfig,
        ]);
    }

    public function httpRequests(Request $request, $id)
    {
        $httpConfig = HttpConfig::findByUid($id);

        return view('admin.http_configs.httpRequests', [
            'httpConfig' => $httpConfig,
        ]);
    }

    public function httpRequestsList(Request $request, $id)
    {
        $httpConfig = HttpConfig::findByUid($id);

        $httpRequests = $httpConfig->httpRequests()
            ->orderBy($request->sort_order, $request->sort_direction ? $request->sort_direction : 'asc')
            ->paginate($request->per_page);

        return view('admin.http_configs.httpRequestsList', [
            'httpConfig' => $httpConfig,
            'httpRequests' => $httpRequests,
        ]);
    }

    public function httpRequestLogs(Request $request, $id)
    {
        $httpRequest = HttpRequest::findByUid($id);

        return view('admin.http_configs.httpRequestLogs', [
            'httpRequest' => $httpRequest,
        ]);
    }

    public function httpRequestLogsList(Request $request, $id)
    {
        $httpRequest = HttpRequest::findByUid($id);
        $httpRequestLogs = $httpRequest->httpRequestLogs()
            ->orderBy($request->sort_order, $request->sort_direction ? $request->sort_direction : 'asc')
            ->paginate($request->per_page);

        return view('admin.http_configs.httpRequestLogsList', [
            'httpRequest' => $httpRequest,
            'httpRequestLogs' => $httpRequestLogs,
        ]);
    }
}
