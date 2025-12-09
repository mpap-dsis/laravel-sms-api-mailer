<?php

namespace Mpap\LaravelSmsApiMailer\Tests;

use Mpap\LaravelSmsApiMailer\Transport\SmsApiTransport;
use PHPUnit\Framework\TestCase;

class SmsApiTransportTest extends TestCase
{
    public function test_transport_can_be_instantiated(): void
    {
        $transport = new SmsApiTransport(
            'http://example.com/api',
            'test-token',
            'test-sistema'
        );

        $this->assertInstanceOf(SmsApiTransport::class, $transport);
    }

    public function test_transport_returns_correct_string_representation(): void
    {
        $transport = new SmsApiTransport(
            'http://example.com/api',
            'test-token',
            'test-sistema'
        );

        $this->assertEquals('smsapi', (string) $transport);
    }
}
