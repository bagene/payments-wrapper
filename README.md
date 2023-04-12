## Payments Wrapper

A simple wrapper for payment gateways like Stripe. This includes an interface for future integration of other payment gateways. Only stripe is implemented right now.

## Installation

### Clone

- Make a folder named `bagene` inside your laravel project and clone this repository inside
- `git clone git@github.com:bagene/payments-wrapper.git`
- add `payments-wrapper` to your composer as local package

```
"repositories": {
    "payments-wrapper": {
        "type": "path",
        "url": "bagene/payments-wrapper",
        "options": {
            "symlink": true
        }
    }
},
```

### Publish Config

- add `\Bagene\PaymentsWrapper\PaymentServiceProvider::class,` to your `app.php` providers.
- run `php artisan vendor:publish` and select `Bagene\PaymentsWrapper\PaymentServiceProvider`

### env()

add these keys to your `.env` file

```
STRIPE_PUBLIC_KEY=
STRIPE_SECRET=
```

## Usage

### initialize Stripe
```
    $stripe = new Stripe();
    $stripe->authenticate();
```

- this will initialize Stripe object and `authenticate()` method will attach your stripe authentication to the stripe request.

### createPayment()

```
    $payment = $stripe->createPayment($payload);
```

#### sample payload 

```
[
    'card' => [
        'number' => '4242424242424242',
        'exp_month' => 4,
        'exp_year' => 2024,
        'cvc' => '314'
    ],
    'amount' => '2000',
    'currency' => 'usd',
    'description' => 'Test'
]
```
- calling `createPayment` will return a Laravel `HttpResponse`.
- you can get the body by calling `$response->json()` and status code with `$response->status()`

### other methods

other methods are available if you wanna customize your payment flow

- `createToken()`
- `createCharge()`
- `captureCharge()`