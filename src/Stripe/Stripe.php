<?php

namespace Bagene\PaymentsWrapper\Stripe;

use Bagene\PaymentsWrapper\Interfaces\Payments;

class Stripe implements Payments
{
	protected string $publicKey;
	protected string $secret;

	public function __construct()
	{
		$this->publicKey = config('payments.stripe.public_key');
		$this->secret = config('payments.stripe.secret');
	}

	public function authenticate()
	{
		return "Bearer {$this->secret}";
	}

	public function createPayment(array $payload)
	{
		
	}
}