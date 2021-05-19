<?php

namespace EscolaLms\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA/Schema(
 *  schema="TransactionRegistrationRequest",
 *  @OA\Property(
 *  )
 * )
 */
class TransactionRegistrationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        /** @var User $user */
        $user = $this->user();
        return $user!=null && $user->can('register:payment-transaction', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }
}
