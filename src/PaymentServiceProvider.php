<?php

namespace Bagene\PaymentsWrapper;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/payments.php' => config_path('payments.php'),
		]);
	}

	public function register()
	{

	}
}