<?php

namespace Acelle\Http\Controllers;

use Acelle\Model\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function delete($uid)
    {
        $paymentMethod = PaymentMethod::findByUid($uid);

        // authorize
        if (\Gate::denies('delete', $paymentMethod)) {
            return $this->notAuthorized();
        }

        //
        $paymentMethod->delete();

        //
        return redirect()->back();
    }
}
