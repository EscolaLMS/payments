<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\Przelewy24GatewayResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;
use Przelewy24\Enums\Currency as Przelewy24Currency;
use Przelewy24\Exceptions\Przelewy24Exception;
use Przelewy24\Przelewy24;

class Przelewy24Driver extends AbstractDriver implements GatewayDriverContract
{
    private Przelewy24 $gateway;

    public function __construct(PaymentsConfig $config)
    {
        $this->config = $config;

        $this->gateway = new Przelewy24(
            $this->config->getPrzelewy24MerchantId(),
            $this->config->getPrzelewy24ApiKey(),
            $this->config->getPrzelewy24Crc(),
            $this->config->getPrzelewy24Live(),
            $this->config->getPrzelewy24PosId(),
        );
    }

    public function purchase(Payment $payment, array $parameters = []): ResponseInterface
    {
        $this->throwExceptionIfMissingParameters($parameters);

        try {
            $response = $this->gateway->transactions()->register(
                sessionId: ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey(),
                amount: $payment->amount,
                description: !empty($payment->description) ? $payment->description : 'Payment',
                email: $parameters['email'],
                urlReturn: $parameters['return_url'],
                currency: Przelewy24Currency::tryFrom($payment->currency) ?? Przelewy24Currency::PLN,
                urlStatus: route('payments-gateway-callback', ['payment' => $payment->getKey()]),
                // methodRefId: md5(rand(0, 100))
            );
        } catch (Przelewy24Exception $exception) {
            return Przelewy24GatewayResponse::fromApiResponseException($exception);
        }

        return Przelewy24GatewayResponse::fromRegisterTransactionResponse($response);
    }

    public function callback(Request $request, array $parameters = []): CallbackResponse
    {
        $callbackNotification = $this->gateway->handleWebhook($request->input());

        try {
            $response = $this->gateway->transactions()->verify(
                sessionId: $callbackNotification->sessionId(),
                orderId: $callbackNotification->orderId(),
                amount: $callbackNotification->amount(),
                currency: $callbackNotification->currency(),
            );
            return new CallbackResponse(true, $callbackNotification->orderId());
        } catch (Przelewy24Exception $exception) {
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
