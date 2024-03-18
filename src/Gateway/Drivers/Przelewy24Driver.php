<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
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
use Ramsey\Uuid\Uuid;

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
        /**
         * 1. Sprawdzenie, czy subskrypcja jeśli nie to standardowy flow transatcion(..., ...)
         * 2a. Sprawdzenie, czy ma tial -> $parameters['has_trail'], ... -> płatność na 1zł, refund, zapis w order, że REFUND ???
         * 2b. W przeciwny wypadku, -> pierwsze kupno subskrypcji
         * 2c. W przeciwny wypadku, -> przedłużenie subskrypcji (inicjowane z paczki cart, cron, pobranie ostatniego order itp)
         */

        $this->throwExceptionIfMissingParameters($parameters);

        try {

            if (isset($parameters['type']) && in_array($parameters['type'], ['subscription', 'subscription-all-in']) && isset($parameters['recursive']) && $parameters['recursive'] === true) {
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

            //dd($callbackNotification, $response);


            return new CallbackResponse(true, $callbackNotification->orderId());
        } catch (Przelewy24Exception $exception) {
            return new CallbackResponse(false, $callbackNotification->orderId(), $exception->getMessage());
        }
    }

    private function transaction(Payment $payment, array $parameters = []): RegisterTransactionResponse
    {
        return $this->gateway->transactions()->register(
            sessionId: ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey() . now()->timestamp,
            amount: $payment->amount,
            description: !empty($payment->description) ? $payment->description : 'Payment',
            email: $parameters['email'],
            urlReturn: $parameters['return_url'],
            currency: Przelewy24Currency::tryFrom($payment->currency) ?? Przelewy24Currency::PLN,
            urlStatus: 'https://webhook-test.com/b12b316c0984853f787c0959f39e365e',
            //urlStatus: route('payments-gateway-callback', ['payment' => $payment->getKey()]),
            urlCardPaymentNotification: 'https://webhook-test.com/b12b316c0984853f787c0959f39e365e',
        );
    }

    private function recursiveTransaction(Payment $payment, array $parameters = []): RegisterTransactionResponse
    {
        $transaction = $this->gateway->transactions()->register(
            sessionId: ($payment->order_id ? $payment->order_id . '_' : '') . $payment->getKey() . now()->timestamp,
            amount: $payment->amount,
            description: !empty($payment->description) ? $payment->description : 'Payment',
            email: $parameters['email'],
            urlReturn: $parameters['return_url'],
            currency: Przelewy24Currency::tryFrom($payment->currency) ?? Przelewy24Currency::PLN,
            urlStatus: 'https://webhook-test.com/b12b316c0984853f787c0959f39e365e',
            //urlStatus: route('payments-gateway-callback', ['payment' => $payment->getKey()]),
            channel: TransactionChannel::CARDS_ONLY->value,
        );




        $response = $this->transaction($payment, $parameters);

        $response = $this->gateway->cards()->cardInfo("4296014872");
        $transaction = $this->transaction($payment, $parameters);

        $response = $this->gateway->cards()->cardCharge($transaction->token());
    }

    public static function requiredParameters(): array
    {
        return [
            'return_url',
            'email'
        ];
    }
}
