<?php

namespace Bagene\PaymentsWrapper\Stripe;

use Bagene\PaymentsWrapper\Interfaces\Payments;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Stripe implements Payments
{
	protected string $url = 'https://api.stripe.com/v1';
	protected string $publicKey;
	protected string $secret;
	protected ?PendingRequest $request;

	public function __construct()
	{
		$this->publicKey = config('payments.stripe.public_key');
		$this->secret = config('payments.stripe.secret');
	}

	public function authenticate()
	{
		$this->request = Http::withToken($this->secret);
	}

	public function testRequest()
	{
		return $this->request->get("$this->url/charges");
	}

	public function createPayment(array $payload)
	{
		
	}
}