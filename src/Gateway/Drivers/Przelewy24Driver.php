<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Responses\CallbackRefundResponse;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\Przelewy24GatewayResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;
use Przelewy24\Api\Requests\Items\RefundItem;
use Przelewy24\Api\Responses\Transaction\RegisterTransactionResponse;
use Przelewy24\Enums\Currency as Przelewy24Currency;
use Przelewy24\Enums\TransactionChannel;
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
            if ($this->hasRecursiveSubscription($parameters)) {
                $response = $this->recursiveTransaction($payment, $parameters);
            }
            else {
                $response = $this->transaction($payment, $parameters);
            }

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

    private function transaction(Payment $payment, array $parameters = []): RegisterTransactionResponse
    {
        return $this->gateway->transactions()->register(
            sessionId: ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey() . $payment->created_at->timestamp,
            amount: $payment->amount,
            description: !empty($payment->description) ? $payment->description : 'Payment',
            email: $parameters['email'],
            urlReturn: $parameters['return_url'],
            currency: Przelewy24Currency::tryFrom($payment->currency) ?? Przelewy24Currency::PLN,
            urlStatus: 'https://webhook-test.com/13d9c3a443a8812727d4700be3fad14e',
            //urlStatus: route('payments-gateway-callback', ['payment' => $payment->getKey()]),
        );
    }

    private function recursiveTransaction(Payment $payment, array $parameters = []): RegisterTransactionResponse
    {
        return $this->gateway->transactions()->register(
            sessionId: ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey() . $payment->created_at->timestamp,
            amount: $payment->amount,
            description: !empty($payment->description) ? $payment->description : 'Payment',
            email: $parameters['email'],
            urlReturn: $parameters['return_url'],
            currency: Przelewy24Currency::tryFrom($payment->currency) ?? Przelewy24Currency::PLN,
            urlStatus: 'https://webhook-test.com/7f9d02b905914e557a5bf05963514140',
            //urlStatus: route('payments-gateway-callback', ['payment' => $payment->getKey()]),
            channel: TransactionChannel::CARDS_ONLY->value,
        );
    }

    public function refund(Request $request, Payment $payment, array $parameters = [])
    {
        try {
            $response = $this->gateway->transactions()->refund(
                requestId: $parameters['gateway_request_id'],
                refunds: [
                    new RefundItem(
                        $payment->gateway_order_id,
                        ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey() . $payment->created_at->timestamp,
                        $payment->amount
                    )
                ],
                refundsUuid: $parameters['gateway_refunds_uuid'],
                urlStatus: 'https://webhook-test.com/7f9d02b905914e557a5bf05963514140',
            );

            //return Przelewy24RefundResponse::fromRegisterTransactionResponse($response);
        } catch (Przelewy24Exception $exception) {
            //return Przelewy24RefundResponse::fromApiResponseException($exception);
            dd($exception);
        }
    }

    public function callbackRefund(Request $request, array $parameters = []): CallbackRefundResponse
    {
        try {
            $refundNotification = $this->gateway->handleRefundWebhook($request->input());

            // todo check status, requestId, refundUUid

            return new CallbackRefundResponse(true, $refundNotification->orderId(), $refundNotification->requestId(), $refundNotification->refundsUuid());
        } catch (Przelewy24Exception $exception) {
            return new CallbackRefundResponse(false, null, null, null, $exception->getMessage());
        }
    }

    public static function requiredParameters(): array
    {
        return [
            'return_url',
            'email'
        ];
    }

    private function hasRecursiveSubscription(array $parameters = []): bool
    {
        if (!$parameters) {
            return false;
        }

        return isset($parameters['type'])
            && in_array($parameters['type'], ['subscription', 'subscription-all-in'])
            && isset($parameters['recursive'])
            && $parameters['recursive'] === true;
    }
}
