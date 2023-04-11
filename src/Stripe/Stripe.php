<?php

namespace Bagene\PaymentsWrapper\Stripe;

use Bagene\PaymentsWrapper\Interfaces\Payments;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Stripe implements Payments
{
	use StripeValidator;

	protected string $url = 'https://api.stripe.com/v1';
	protected string $publicKey;
	protected string $secret;
	protected ?PendingRequest $request;

	public function __construct()
	{
		$this->publicKey = config('payments.stripe.public_key');
		$this->secret = config('payments.stripe.secret');
	}

	/**
	 * Attach Authentication Token to the Request
	 */
	public function authenticate(): void
	{
		$this->request = Http::withToken($this->secret)->asForm();
	}

	/**
	 * Test connection
	 */
	public function testRequest(): Response
	{
		return $this->request->get("$this->url/charges");
	}

	/**
	 * General Function for creating payment
	 * @param array $payload
	 * @return mixed
	 */
	public function createPayment(array $payload): mixed
	{
		$token = $this->createToken($payload);
		$payload['source'] = $token->json(['id']);
		return $this->createCharge($payload);
	}

	/**
	 * Create Payment Token
	 * @param array $payload
	 * @return Response
	 */
	public function createToken(array $payload): Response
	{
		$payload = $this->validateToken($payload);
		return $this->request->post("$this->url/tokens", $payload);
	}

	/**
	 * Create Payment Charge
	 * @param array $payload
	 * @return Response
	 */
	public function createCharge($payload): Response
	{
		$payload = $this->validateCharge($payload);
		return $this->request->post("$this->url/charges", $payload);
	}

	/**
	 * Capture uncaptured charge
	 * @param string $id
	 * @return Response
	 */
	public function captureCharge(string $id): Response
	{
		return $this->request->post("$this->url/charges/$id/capture");
	}
}