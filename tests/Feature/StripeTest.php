<?php

namespace Bagene\PaymentsWrapper\Tests;

use Bagene\PaymentsWrapper\Stripe\Stripe;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class StripeTest extends \Orchestra\Testbench\TestCase
{
	protected $loadEnvironmentVariables = true;

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

	/** @test */
	public function it_can_authenticate()
	{
		$expected = 'Bearer Test';

		$stripe = new Stripe();

		$auth = $stripe->authenticate();

		$response = $stripe->testRequest();

		$this->assertEquals($response->status(), 200);
		$this->assertIsArray($response->json());
	}
}