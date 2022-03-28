<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\Przelewy24GatewayResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;
use Przelewy24\Exceptions\ApiResponseException;
use Przelewy24\Przelewy24;
use Przelewy24\TransactionStatusNotification;

class Przelewy24Driver extends AbstractDriver implements GatewayDriverContract
{
    private Przelewy24 $gateway;

    public function __construct(PaymentsConfig $config)
    {
        $this->config = $config;

        $this->gateway = new Przelewy24([
            'api_key' => $this->config->getPrzelewy24ApiKey(),
            'crc' => $this->config->getPrzelewy24Crc(),
            'live' => $this->config->getPrzelewy24Live(),
            'merchant_id' => $this->config->getPrzelewy24MerchantId(),
            'pos_id' => $this->config->getPrzelewy24PosId(),
        ]);
    }

    public function purchase(Payment $payment, array $parameters = []): ResponseInterface
    {
        $this->throwExceptionIfMissingParameters($parameters);

        try {
            $response = $this->gateway->transaction([
                'amount' => $payment->amount,
                'currency' => (string) ($payment->currency ?? $this->config->getDefaultCurrency()),
                'description' => $payment->description,
                'url_return' => $parameters['return_url'],
                'url_status' => route('payments-gateway-callback', ['payment' => $payment->getKey()]),
                'email' => $parameters['email'],
                'session_id' => ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey(),
            ]);
        } catch (ApiResponseException $exception) {
            return Przelewy24GatewayResponse::fromApiResponseException($exception);
        }

        return Przelewy24GatewayResponse::fromRegisterTransactionResponse($response);
    }

    public function callback(Request $request, array $parameters = []): CallbackResponse
    {
        $callbackNotification = new TransactionStatusNotification($request->input());

        try {
            $response = $this->gateway->verify([
                'session_id' => $callbackNotification->sessionId(),
                'order_id' => $callbackNotification->orderId(),
                'amount' => $callbackNotification->amount(),
                'currency' => $callbackNotification->currency(),
            ]);
            return new CallbackResponse(true, $callbackNotification->orderId());
        } catch (ApiResponseException $exception) {
            return new CallbackResponse(false, $callbackNotification->orderId(), $exception->getMessage());
        }
    }

    public static function requiredParameters(): array
    {
        return [
            'return_url',
            'email'
        ];
    }
}
