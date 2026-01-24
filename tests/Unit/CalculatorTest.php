<?php

declare(strict_types=1);

namespace YourVendor\YourPackage\Tests\Unit;

use PHPUnit\Framework\TestCase;
use YourVendor\YourPackage\Calculator;

/**
 * Unit tests for the Calculator class.
 *
 * Unit tests focus on testing individual methods in isolation.
 */
class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testAdd(): void
    {
        $result = $this->calculator->add(2.5, 3.7);
        $this->assertEqualsWithDelta(6.2, $result, 0.001);
    }

    public function testSubtract(): void
    {
        $result = $this->calculator->subtract(10.0, 4.5);
        $this->assertEqualsWithDelta(5.5, $result, 0.001);
    }

    public function testMultiply(): void
    {
        $result = $this->calculator->multiply(3.0, 4.0);
        $this->assertEqualsWithDelta(12.0, $result, 0.001);
    }

    public function testDivide(): void
    {
        $result = $this->calculator->divide(15.0, 3.0);
        $this->assertEqualsWithDelta(5.0, $result, 0.001);
    }

    public function testDivideByZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero is not allowed');

        $this->calculator->divide(10.0, 0.0);
    }

    public function testPower(): void
    {
        $result = $this->calculator->power(2.0, 3.0);
        $this->assertEqualsWithDelta(8.0, $result, 0.001);
    }

    /**
     * @dataProvider additionProvider
     */
    public function testAddWithDataProvider(float $a, float $b, float $expected): void
    {
        $result = $this->calculator->add($a, $b);
        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    /**
     * @return array<string, array{float, float, float}>
     */
    public static function additionProvider(): array
    {
        return [
            'positive numbers' => [1.0, 2.0, 3.0],
            'negative numbers' => [-1.0, -2.0, -3.0],
            'mixed numbers' => [1.0, -2.0, -1.0],
            'zero values' => [0.0, 5.0, 5.0],
            'decimal values' => [1.5, 2.7, 4.2],
        ];
    }
}
