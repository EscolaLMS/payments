<?php

namespace EscolaLms\Payments\Http\Requests;

use BenSampo\Enum\Rules\EnumValue;
use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentsSearchRequest extends FormRequest
{
    public function authorize()
    {
        return !empty($this->user());
    }

    public function rules()
    {
        return [
            'status' => ['nullable', new EnumValue(PaymentStatus::class, false)],
            'payable_id' => ['sometimes', 'nullable'],
            'payable_type' => ['sometimes', 'nullable', 'string'],
            'order_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'status', 'payable_id', 'billable_id', 'amount'])]
        ];
    }

    protected function passedValidation()
    {
        $user = $this->user();
        $this->merge([
            'billable_id' => $user->getKey(),
            'billable_type' => $user instanceof Billable ? get_class($user) : null
        ]);
    }
}
