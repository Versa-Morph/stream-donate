<?php

namespace Tests;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\FakePaymentGateway;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(PaymentGatewayInterface::class, FakePaymentGateway::class);
    }
}
