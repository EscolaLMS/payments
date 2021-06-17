<?php

namespace EscolaLms\Payments\Dtos;

use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use Illuminate\Http\Request;

class PaymentMethodDto implements DtoContract, PaymentMethodContract, InstantiateFromRequest
{
    private string $paymentMethodId;

    /**
     * PaymentMethodDto constructor.
     * @param string $paymentMethodId
     */
    public function __construct(string $paymentMethodId)
    {
        $this->paymentMethodId = $paymentMethodId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->paymentMethodId
        ];
    }

    /**
     * @return string
     */
    public function getPaymentMethodId(): string
    {
        return $this->paymentMethodId;
    }

    public static function instantiateFromRequest(Request $request): self
    {
        return new self($request->get('paymentMethodId') ?? '');
    }
}
