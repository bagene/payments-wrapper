<?php

namespace Bagene\PaymentsWrapper\Tests;

use Bagene\PaymentsWrapper\Stripe\Stripe;
use Illuminate\Validation\ValidationException;

class StripeTest extends \Orchestra\Testbench\TestCase
{
	protected $loadEnvironmentVariables = true;
	protected ?Stripe $stripe;

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function defineEnvironment($app)
	{
		// setup stripe config
		$app['config']->set('payments.stripe', [
			'public_key' => env('STRIPE_PUBLIC_KEY'),
			'secret' => env('STRIPE_SECRET'),
		]);
	}

	/**
	 * Initialize and Authenticate Stripe
	 */
	protected function initStripe()
	{
		$this->stripe = new Stripe();
		$this->stripe->authenticate();
	}

	/** @test */
	public function token_validator_should_return_exception()
	{
		$this->initStripe();
		$this->expectException(ValidationException::class);

		$this->stripe->validateToken([
			'number' => '424242424242424', // should be 16 digits instead of 15 like this
			'exp_month' => 4,
			'exp_year' => 2024,
			'cvc' => '314',
		]);
	}

	/** @test */
	public function charge_validator_should_return_exception()
	{
		$this->initStripe();
		$this->expectException(ValidationException::class);

		$this->stripe->validateCharge([
			'amount' => 2000,
			'currency' => 'usd',
			'source' => 'test', // should start with tok_
			'description' => 'Test Charge',
		]);
	}

	/** @test */
	public function it_can_authenticate()
	{
		$this->initStripe();

		$response = $this->stripe->testRequest();

		$this->assertEquals($response->status(), 200);
		$this->assertIsArray($response->json());
	}

	/** @test */
	public function it_can_create_payment_token()
	{
		$this->initStripe();

		$response = $this->stripe->createToken([
			'card' => [
				'number' => '4242424242424242',
				'exp_month' => 4,
				'exp_year' => 2024,
				'cvc' => '314',
			],
		]);

		$this->assertEquals($response->status(), 200);
		$this->assertArrayHasKey('card', $response->json());
		$this->assertStringStartsWith('tok_', $response->json('id'));
	}

	/** @test */
	public function it_can_create_payment_charge()
	{
		$this->initStripe();

		$response = $this->stripe->createToken([
			'card' => [
				'number' => '4242424242424242',
				'exp_month' => 4,
				'exp_year' => 2024,
				'cvc' => '314',
			],
		]);

		$response = $this->stripe->createCharge([
			'amount' => 2000,
			'currency' => 'usd',
			'source' => $response->json('id'),
			'description' => 'Test Charge',
		]);

		
		$this->assertEquals($response->status(), 200);
		$this->assertStringStartsWith('ch_', $response->json('id'));
	}

	/** @test */
	public function it_can_capture_payment_charge()
	{
		$this->initStripe();

		$response = $this->stripe->createToken([
			'card' => [
				'number' => '4242424242424242',
				'exp_month' => 4,
				'exp_year' => 2024,
				'cvc' => '314',
			],
		]);

		$response = $this->stripe->createCharge([
			'amount' => 2000,
			'currency' => 'usd',
			'source' => $response->json('id'),
			'description' => 'Test Charge',
			'capture' => "false",
		]);
		$this->assertFalse($response->json('captured'));

		$response = $this->stripe->captureCharge($response->json('id'));
		$this->assertTrue($response->json('captured'));
	}
}