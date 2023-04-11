<?php

namespace Bagene\PaymentsWrapper\Stripe;

use Illuminate\Support\Facades\Validator;

trait StripeValidator
{
    /**
     * Validate Token Payload
     * @param array $payload
     * @return array
     */
    public function validateToken(array $payload): array
    {
        return Validator::make($payload, [
            'card.number' => 'required|string|min:16|max:16',
            'card.exp_month' => 'required|int',
            'card.exp_year' => 'required|int',
            'card.cvc' => 'required|string|min:3|max:3',
        ])->validate();
    }

    /**
     * Validate Charge Payload
     * @param array $payload
     * @return array
     */
    public function validateCharge(array $payload): array
    {
        return Validator::make($payload, [
            'amount' => 'required|numeric',
            'currency' => 'required|in:usd,eur',
            'source' => 'nullable|string|starts_with:tok_',
            'description' => 'string|nullable',
            'capture' => 'nullable|string',
        ])->validate();
    }
}