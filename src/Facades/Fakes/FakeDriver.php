<?php

namespace EscolaLms\Payments\Facades\Fakes;

use EscolaLms\Payments\Dtos\WebHookDto;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\AbstractDriver;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Drivers\Contracts\WebHookDriverContract;
use EscolaLms\Payments\Gateway\Drivers\Przelewy24Driver;
use EscolaLms\Payments\Gateway\Drivers\StripeDriver;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\NoneGatewayResponse;
use EscolaLms\Payments\Gateway\Responses\StripeIntentResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\StripeObject;

class FakeDriver extends AbstractDriver implements GatewayDriverContract, WebHookDriverContract
{
    protected ?string $requested_driver = null;

    public function __construct(PaymentsConfig $config, ?string $requested_driver = null)
    {
        parent::__construct($config);
        $this->requested_driver = $requested_driver;
    }

    public static function requiredParameters(): array
    {
        return [];
    }

    public function purchase(Payment $payment, array $parameters = []): ResponseInterface
    {
        if ($this->requested_driver === 'stripe-intent') {
            $intent = new PaymentIntent();
            $intent->client_secret = 'test_client_secret';
            return new StripeIntentResponse($intent); // todo: albo zrobić że intent jest opcjonalny, albo stworzyć FakeIntent który tu można wsadzić, albo zrobić oosbną klasę StripeIntentFakeResponse()
        }
        $this->throwExceptionIfMissingParameters($parameters);
        return new NoneGatewayResponse();
    }

    public function callback(Request $request, array $parameters = []): CallbackResponse
    {
        return new CallbackResponse();
    }

    public function getRequestedDriver(): ?string
    {
        return $this->requested_driver;
    }

    public function getWebhookData(Request $request): WebHookDto
    {
        $payment = Payment::query()->where('client_secret', 'test_client_secret')->first();

        $fakePaymentIntent = [
            'id' => 'test_client_secret',
            'object' => 'payment_intent',
        ];

        $event = new Event();
        $event->type = Event::PAYMENT_INTENT_SUCCEEDED;
        $event->data = new StripeObject();
        $event->data->object = new StripeObject($fakePaymentIntent);

        return new WebHookDto($payment, $event);
    }

    protected function getRequiredParameters(): array
    {
        switch ($this->requested_driver) {
            case 'stripe':
                return StripeDriver::requiredParameters();
            case 'przelewy24':
                return Przelewy24Driver::requiredParameters();
            default:
                return self::requiredParameters();
        }
    }
}
