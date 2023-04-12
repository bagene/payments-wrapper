<?php

namespace Bagene\PaymentsWrapper\Stripe;

use Bagene\PaymentsWrapper\Exceptions\StripeException;
use Bagene\PaymentsWrapper\Interfaces\Payments;
use Exception;
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
		$charge =  $this->createCharge($payload);

		if ($charge->status() > 299) {
			throw new StripeException($charge->json('error.message'), $charge->status());
		}

		return $charge;
	}

	/**
	 * Create Payment Token
	 * @param array $payload
	 * @return Response
	 */
	public function createToken(array $payload): Response
	{
		$payload = $this->validateToken($payload);
		$token =  $this->request->post("$this->url/tokens", $payload);
		if ($token->status() > 299) {
			throw new StripeException($token->json('error.message'), $token->status());
		}
		return $token;
	}

	/**
	 * Create Payment Charge
	 * @param array $payload
	 * @return Response
	 */
	public function createCharge($payload): Response
	{
		$payload = $this->validateCharge($payload);
		$charge = $this->request->post("$this->url/charges", $payload);
		if ($charge->status() > 299) {
			throw new StripeException($charge->json('error.message'), $charge->status());
		}

		return $charge;
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