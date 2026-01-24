<?php

declare(strict_types=1);

namespace YourVendor\YourPackage;

/**
 * A simple calculator class for demonstration purposes.
 */
class Calculator
{
    /**
     * Add two numbers.
     */
    public function add(float $a, float $b): float
    {
        return $a + $b;
    }

    /**
     * Subtract two numbers.
     */
    public function subtract(float $a, float $b): float
    {
        return $a - $b;
    }

    /**
     * Multiply two numbers.
     */
    public function multiply(float $a, float $b): float
    {
        return $a * $b;
    }

    /**
     * Divide two numbers.
     *
     * @throws \InvalidArgumentException When dividing by zero
     */
    public function divide(float $a, float $b): float
    {
        if ($b === 0.0) {
            throw new \InvalidArgumentException('Division by zero is not allowed');
        }

        return $a / $b;
    }

    /**
     * Calculate the power of a number.
     */
    public function power(float $base, float $exponent): float
    {
        return pow($base, $exponent);
    }
}
