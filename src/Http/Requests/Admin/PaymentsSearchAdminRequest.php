<?php

namespace EscolaLms\Payments\Http\Requests\Admin;

use BenSampo\Enum\Rules\EnumValue;
use EscolaLms\Core\Enums\UserRole;
use EscolaLms\Payments\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentsSearchAdminRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->hasRole(UserRole::ADMIN);
    }

    public function rules()
    {
        return [
            'status' => ['nullable', new EnumValue(PaymentStatus::class, false)],
            'payable_id' => ['sometimes', 'nullable'],
            'payable_type' => ['sometimes', 'nullable', 'string'],
            'billable_id' => ['sometimes', 'nullable'],
            'billable_type' => ['sometimes', 'nullable', 'string'],
            'order_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'status', 'payable_id'])]
        ];
    }
}
