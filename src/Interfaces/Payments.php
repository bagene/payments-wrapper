<?php

namespace Bagene\PaymentsWrapper\Interfaces;

interface Payments
{
	public function authenticate();
	/**
	 * Create Payment and Send
	 */
	public function createPayment(array $payload);
}