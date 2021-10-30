# Payments

[![swagger](https://img.shields.io/badge/documentation-swagger-green)](https://escolalms.github.io/payments/)
[![codecov](https://codecov.io/gh/EscolaLMS/Files/branch/main/graph/badge.svg?token=NRAN4R8AGZ)](https://codecov.io/gh/EscolaLMS/payments)
[![phpunit](https://github.com/EscolaLMS/payments/actions/workflows/test.yml/badge.svg)](https://github.com/EscolaLMS/payments/actions/workflows/test.yml)
[![downloads](https://img.shields.io/packagist/dt/escolalms/payments)](https://packagist.org/packages/escolalms/payments)
[![downloads](https://img.shields.io/packagist/v/escolalms/payments)](https://packagist.org/packages/escolalms/payments)
[![downloads](https://img.shields.io/packagist/l/escolalms/payments)](https://packagist.org/packages/escolalms/payments)
[![Maintainability](https://api.codeclimate.com/v1/badges/e42a94f20c76b719fc38/maintainability)](https://codeclimate.com/github/EscolaLMS/payments/maintainability)

## Facades

### Payments Facade

Use this facade for starting payment processing.
You can create PaymentProcessor` either from a model using Payable trait or from precreated Payment object.

```php
use EscolaLms\Cart\Models\Cart;
use EscolaLms\Payments\Dtos\PaymentMethodDto;
use EscolaLms\Payments\Facades\Payments;

$payable = Cart::find($id); // Cart must implement Payable interface and use Payable trait
$paymentMethodDto = PaymentMethodDto::instantiateFromRequest($request);
$processor = Payments::processPayment($payable);
$processor->purchase($paymentMethodDto); // will emit PaymentPaid event on success
if($payment->status->is(PaymentStatus::PAID)){
    // ...
}
```

### PaymentGateway Facade

With this facade you can call payment provider gateways directly.

For existing payment you can for example do:

```php
use EscolaLms\Payments\Dtos\PaymentMethodDto;
use EscolaLms\Payments\Facades\PaymentGateway;
use EscolaLms\Payments\Models\Payment;

$payment = Payment::find($id);
$paymentMethodDto = PaymentMethodDto::instantiateFromRequest($request);
$paymentDto = PaymentDto::instantiateFromPayment($payment); // or you can create it manually
PaymentGateway::purchase($paymentDto, $paymentMethodDto); // will use default payment driver
```

**Important**: This will not save `Payment` object.

To use specific driver, you can call

```
PaymentGateway::driver('stripe')->purchase($paymentDto, $paymentMethodDto);
```

#### Available payemtn drivers:

-   **stripe** (using `Stripe Payment Intent`)
-   **free**
-   TODO: _stripe-checkout_

## Traits

### Billable

`Billable` trait and interface must be included in `User` model (or any other model that represents an entity that pays for the Payment).

### Payable

`Payable` trait and interface are the core of this package, enabling simplified calling of `PaymentsService` and `GatewayManager`.
When you include it in your model that represents a `Payable` (for example `Cart` or `Order` or `Product`) you can begin payment processing for that `Payable` by calling `$payable->process()`
which calls `Payments::processPayable($this)` and automatically creates a `Payment` and returns a `PaymentProcessor` instance for that Payment.

## PaymentProcessor

Let's you process payments, with automatically setting payment status after purchase and emiting events.

```php
use EscolaLms\Payments\Dtos\PaymentMethodDto;
use EscolaLms\Payments\Entities\PaymentProcessor;
use EscolaLms\Payments\Models\Payment;

$payment = Payment::find($id);
$paymentMethodDto = PaymentMethodDto::instantiateFromRequest($request);
$processor = new PaymentProcessor($payment); // instead of using Payments facade
$processor->purchase($paymentMethodDto);
```

`PaymentProcessor` automatically selects `free` driver when payment amount equals 0.
