<?php

namespace Tests\Unit;

use App\Services\Payment\PaymentGatewayInterface;
use Tests\Support\FakePaymentGateway;
use Tests\TestCase;

class PaymentGatewayBindingTest extends TestCase
{
    public function test_fake_gateway_is_bound_during_tests(): void
    {
        $gateway = $this->app->make(PaymentGatewayInterface::class);

        $this->assertInstanceOf(FakePaymentGateway::class, $gateway);
    }
}
