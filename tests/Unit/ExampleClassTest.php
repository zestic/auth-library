<?php

declare(strict_types=1);

namespace YourVendor\YourPackage\Tests\Unit;

use PHPUnit\Framework\TestCase;
use YourVendor\YourPackage\ExampleClass;

/**
 * Unit tests for ExampleClass - replace with your actual tests
 */
class ExampleClassTest extends TestCase
{
    private ExampleClass $exampleClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exampleClass = new ExampleClass();
    }

    public function testExampleMethod(): void
    {
        $result = $this->exampleClass->exampleMethod();
        
        $this->assertIsString($result);
        $this->assertEquals("Hello from your library!", $result);
    }

    public function testConstructor(): void
    {
        $instance = new ExampleClass();
        
        $this->assertInstanceOf(ExampleClass::class, $instance);
    }
}
