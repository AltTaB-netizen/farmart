<?php

namespace Botble\Payment\Http\Requests;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $key = get_payment_setting_key('available_countries', $this->input('type'));

        $this->merge([
            $key => $this->input($key, []),
        ]);
    }

    public function rules(): array
    {
        \Log::info('🛠 PaymentMethodRequest Submitted Data', [
            'all' => $this->all(),
            'type' => $this->input('type'),
        ]);
    
        return [
            'type' => ['required', 'string', 'max:120', Rule::in(PaymentMethodEnum::values())],
            ...PaymentHelper::getPaymentMethodRules($this->input('type')),
        ];
    }
}
