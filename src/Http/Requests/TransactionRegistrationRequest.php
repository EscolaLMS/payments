<?php

namespace EscolaLms\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Alcohol\ISO4217;

/**
 * @OA\Schema(
 *     schema="TransactionRegistrationRequest",
 *     required={"amount","currency"},
 *     @OA\Property(
 *          property="amount",
 *          type="integer",
 *          description="amount of currency to charge"
 *     ),
 *     @OA\Property(
 *          property="currency",
 *          type="string",
 *          description="Currency code defined as alpha 3 in ISO 4217"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         maxLength=255,
 *         description="short description text that will show up in payment dialog"
 *     ),
 * )
 */
class TransactionRegistrationRequest extends FormRequest
{
    private ISO4217 $currencyParser;

    public function __construct(ISO4217 $currencyParser)
    {
        parent::__construct();
        $this->currencyParser = $currencyParser;
    }

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
            'amount' => 'integer|min:0',
            'currency' => fn($field,$value,$fail) =>
                empty($this->currencyParser->getByCode($value))
                    ? $fail(sprintf("The currency '%s' is invalid",$value))
                    : null,
            'description' => 'string|max:255',
        ];
    }

    public function getParamAmount(): int
    {
        return $this->get('amount');
    }

    public function getParamCurrency(): string
    {
        $stringValue = $this->get('currency');
        return $this->currencyParser->getByCode($stringValue)['alpha3'];
    }

    public function getParamDescription(): string
    {
        return $this->get('description');
    }
}
