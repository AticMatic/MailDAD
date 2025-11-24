<?php

/**
 * Plan class.
 *
 * Model class for Plan
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Acelle\Library\Contracts\PlanInterface;

class PlanVerification extends Plan implements PlanInterface
{
    protected $table = 'plans';

    // Type
    public const TYPE_VERIFICATION = 'verification';

    // handle when subscription activated
    public function handleSubscriptionActivatedSuccess(Subscription $subscription)
    {
        // @todo: add email verification credits?
    }

    // handle when subscription renewed
    public function handleSubscriptionRenewedSuccess(Subscription $subscription)
    {
        // @todo: reset email veridication credits for new period?
    }

    // handle when plan changed
    public function handlePlanChangedSuccess(Subscription $subscription)
    {
        // @todo: calculate email veridication credits for plan changed? new plan credits - used credits?
    }

    // Main scope
    public static function scopeVerification($query)
    {
        $query = $query->where('TYPE', self::TYPE_VERIFICATION);
    }

    public static function newDefault()
    {
        $plan = new self([
            'price' => 0,
            'billing_cycle' => 'monthly',
            'frequency_amount' => 1,
            'frequency_unit' => 'month',
            'trial_amount' => '0',
            'trial_unit' => 'day',
            'vat' => 0
        ]);
        $plan->status = self::STATUS_ACTIVE;
        $plan->type = self::TYPE_VERIFICATION;
        return $plan;
    }

    public static function scopeSearch($query, $keyword)
    {
        $keyword = strtolower(trim($keyword));

        // search by keyword
        if ($keyword) {
            $query =  $query->whereRaw('LOWER(name) LIKE ? OR LOWER(description) LIKE ?', '%'.$keyword.'%', '%'.$keyword.'%');
        }
    }

    public function getBillingCycleSelectOptions()
    {
        $options = [];

        foreach (self::billingCycleValues() as $key => $data) {
            $wording = trans('messages.time.billing_cycle.'.$key);
            $options[] = ['text' => $wording, 'value' => $key];
        }

        // Custom
        $options[] = ['text' => trans('messages.time.billing_cycle.custom'), 'value' => 'custom'];

        return $options;
    }

    public function saveFromParams(
        $name,
        $description,
        $email_verification_credits,
        $price,
        $currency_id,
        $frequency_amount,
        $frequency_unit,
        $trial_amount,
        $trial_unit
    ) {
        // fill
        $this->name = $name;
        $this->description = $description;
        $this->email_verification_credits = $email_verification_credits;
        $this->price = $price;
        $this->currency_id = $currency_id;
        $this->frequency_amount = $frequency_amount;
        $this->frequency_unit = $frequency_unit;
        $this->trial_amount = $trial_amount;
        $this->trial_unit = $trial_unit;

        $rules = [
            'name'   => ['required'],
            'description'   => ['required'],
            'email_verification_credits'   => ['required'],
            'price'   => ['required'],
            'currency_id' => ['required'],
            'frequency_amount' => ['required'],
            'frequency_unit' => ['required'],
            'trial_amount' => ['required'],
            'trial_unit' => ['required'],
        ];

        // validation
        $validator = \Validator::make($this->getAttributes(), $rules);

        // check if has errors
        if ($validator->fails()) {
            return $validator;
        }

        //
        $this->options = '[]';

        // save to db
        $this->save();

        // return false
        return $validator;
    }
}
