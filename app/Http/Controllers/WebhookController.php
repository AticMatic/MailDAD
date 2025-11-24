<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;
use Acelle\Model\Webhook;

class WebhookController extends Controller
{
    public function test(Request $request, $id)
    {
        $webhook = Webhook::findByUid($id);

        if ($request->isMethod('post')) {
            $httpRequest = $webhook->test($request->webhook);

            return view('webhooks.testResult', [
                'webhook' => $webhook,
                'httpRequestLog' => $httpRequest->httpRequestLogs()->latest()->first(),
            ]);
        }

        return view('webhooks.test', [
            'webhook' => $webhook,
        ]);
    }
}
