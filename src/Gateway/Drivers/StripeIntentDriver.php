<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Dtos\WebHookDto;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Drivers\Contracts\WebHookDriverContract;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\StripeIntentResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Omnipay\Common\Message\ResponseInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeIntentDriver extends AbstractDriver implements GatewayDriverContract, WebHookDriverContract
{
    public static function requiredParameters(): array
    {
        return [];
    }

    /**
     * @throws ApiErrorException
     */
    public function purchase(Payment $payment, array $parameters = []): ResponseInterface
    {
        $stripe = new StripeClient($this->config->getStripeSecretKey());

        $intent = $stripe->paymentIntents->create([
            'amount' => $payment->amount,
            'currency' => (string)($payment->currency ?? $this->config->getDefaultCurrency()),
            'metadata' => [
                'order_id' => $payment->order_id,
                'payment_id' => $payment->getKey(),
            ],
        ]);

        return new StripeIntentResponse($intent);
    }

    /**
     * @throws SignatureVerificationException
     */
    public function getWebhookData(Request $request): WebHookDto
    {
        $event = Webhook::constructEvent(
            $request->getContent(),
            $request->header('HTTP_STRIPE_SIGNATURE'),
            Config::get('services.stripe.webhook.secret')
        );

        $payment = Payment::query()->where('client_secret', $event->data->object->client_secret)->firstOrFail();

        return new WebHookDto($payment, $event);
    }

    public function callback(Request $request, array $parameters = []): CallbackResponse
    {
        return new CallbackResponse();
    }
}
