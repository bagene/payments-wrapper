<?php

namespace Bagene\PaymentsWrapper\Tests;

use Bagene\PaymentsWrapper\Stripe\Stripe;
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
		// Setup default database to use sqlite :memory:
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

		$this->assertEquals($expected, $auth);
	}
}