<?php

declare(strict_types=1);

namespace YourVendor\YourPackage\Tests\Integration;

use YourVendor\YourPackage\Calculator;

/**
 * Integration tests for the Calculator class.
 *
 * Integration tests focus on testing how components work together
 * and may involve more complex scenarios or external dependencies.
 */
class CalculatorIntegrationTest extends IntegrationTestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testComplexCalculationWorkflow(): void
    {
        // Test a complex calculation workflow that uses multiple operations
        $step1 = $this->calculator->add(10.0, 5.0); // 15.0
        $step2 = $this->calculator->multiply($step1, 2.0); // 30.0
        $step3 = $this->calculator->subtract($step2, 5.0); // 25.0
        $step4 = $this->calculator->divide($step3, 5.0); // 5.0
        $result = $this->calculator->power($step4, 2.0); // 25.0

        $this->assertWithinDelta(25.0, $result);
    }

    public function testCalculatorWithRealWorldScenario(): void
    {
        // Simulate calculating compound interest
        $principal = 1000.0;
        $rate = 0.05; // 5%
        $time = 3.0; // 3 years

        // A = P(1 + r)^t
        $step1 = $this->calculator->add(1.0, $rate); // 1.05
        $step2 = $this->calculator->power($step1, $time); // 1.157625
        $finalAmount = $this->calculator->multiply($principal, $step2); // 1157.625

        $this->assertWithinDelta(1157.625, $finalAmount);
    }

    public function testErrorHandlingInWorkflow(): void
    {
        // Test that errors are properly handled in a workflow
        $this->expectException(\InvalidArgumentException::class);

        $step1 = $this->calculator->add(10.0, 5.0);
        $step2 = $this->calculator->subtract($step1, 15.0); // Results in 0

        // This should throw an exception
        $this->calculator->divide(100.0, $step2);
    }

    public function testCalculatorStateIndependence(): void
    {
        // Test that calculator operations don't affect each other
        $calc1Result = $this->calculator->add(5.0, 3.0);
        $calc2Result = $this->calculator->multiply(2.0, 4.0);
        $calc3Result = $this->calculator->add(5.0, 3.0); // Same as calc1

        $this->assertWithinDelta(8.0, $calc1Result);
        $this->assertWithinDelta(8.0, $calc2Result);
        $this->assertWithinDelta($calc1Result, $calc3Result);
    }

    /**
     * @dataProvider complexScenarioProvider
     *
     * @param array<int, array{string, array<float>}> $operations
     */
    public function testComplexScenariosWithDataProvider(
        array $operations,
        float $expected
    ): void {
        $result = 0.0;

        foreach ($operations as $operation) {
            [$method, $args] = $operation;

            if ($method === 'start') {
                $result = $args[0];
            } else {
                $result = $this->calculator->$method($result, ...$args);
            }
        }

        $this->assertWithinDelta($expected, $result);
    }

    /**
     * @return array<string, array{array<int, array{string, array<float>}>, float}>
     */
    public static function complexScenarioProvider(): array
    {
        return [
            'basic arithmetic chain' => [
                [
                    ['start', [10.0]],
                    ['add', [5.0]],
                    ['multiply', [2.0]],
                    ['subtract', [10.0]],
                ],
                20.0, // (10 + 5) * 2 - 10 = 20
            ],
            'power and division chain' => [
                [
                    ['start', [4.0]],
                    ['power', [2.0]],
                    ['divide', [2.0]],
                ],
                8.0, // 4^2 / 2 = 8
            ],
        ];
    }
}
