<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Zestic\Auth\Context\RegistrationContext;

class RegistrationContextTest extends TestCase
{
    public function testToArrayReturnsExpectedData(): void
    {
        $data = [
            'email' => 'test@example.com',
            'additionalData' => ['foo' => 'bar'],
        ];
        $context = new RegistrationContext($data);
        $this->assertSame($data, $context->toArray());
    }

    public function testToArrayMissingAdditionalData(): void
    {
        $data = [
            'email' => 'test@example.com',
        ];
        $context = new RegistrationContext($data);
        $output = $context->toArray();
        $this->assertIsArray($output['additionalData']);
    }

    public function testGetAndSetMethods(): void
    {
        $data = [
            'email'          => 'test@example.com',
            'additionalData' => ['foo' => 'bar'],
        ];
        $context = new RegistrationContext($data);
        $this->assertSame('test@example.com', $context->get('email'));
        $context->set('email', 'new@example.com');
        $this->assertSame('new@example.com', $context->get('email'));
    }

    public function testExtractAndRemove(): void
    {
        $data = [
            'email' => 'test@example.com',
            'additionalData' => ['foo' => 'bar'],
        ];
        $context = new RegistrationContext($data);
        $value = $context->extractAndRemove('email');
        $this->assertSame('test@example.com', $value);
        $this->assertNull($context->get('email'));
    }
}
