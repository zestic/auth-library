<?php

declare(strict_types=1);

namespace Zestic\Auth\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Abstract base class for integration tests.
 *
 * This class provides common functionality and setup for integration tests,
 * including shared utilities, fixtures, and configuration that integration
 * tests might need.
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * Set up common integration test environment.
     *
     * Override this method in child classes if additional setup is needed,
     * but remember to call parent::setUp().
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Common integration test setup can go here
        // Examples:
        // - Database connections
        // - External service mocks
        // - File system setup
        // - Environment configuration
    }

    /**
     * Clean up after integration tests.
     *
     * Override this method in child classes if additional cleanup is needed,
     * but remember to call parent::tearDown().
     */
    protected function tearDown(): void
    {
        // Common integration test cleanup can go here
        // Examples:
        // - Database cleanup
        // - Temporary file removal
        // - Service disconnections

        parent::tearDown();
    }

    /**
     * Helper method to create temporary files for testing.
     *
     * @param string $content The content to write to the temporary file
     * @param string $suffix  Optional file suffix/extension
     *
     * @return string The path to the created temporary file
     */
    protected function createTemporaryFile(string $content = '', string $suffix = '.tmp'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'integration_test_') . $suffix;
        file_put_contents($tempFile, $content);

        // Register for cleanup
        $this->addToAssertionCount(0); // Prevent risky test warning
        register_shutdown_function(function () use ($tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        });

        return $tempFile;
    }

    /**
     * Helper method to create a temporary directory for testing.
     *
     * @return string The path to the created temporary directory
     */
    protected function createTemporaryDirectory(): string
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'integration_test_' . uniqid();
        mkdir($tempDir, 0777, true);

        // Register for cleanup
        $this->addToAssertionCount(0); // Prevent risky test warning
        register_shutdown_function(function () use ($tempDir) {
            if (is_dir($tempDir)) {
                $this->removeDirectory($tempDir);
            }
        });

        return $tempDir;
    }

    /**
     * Recursively remove a directory and its contents.
     *
     * @param string $dir The directory to remove
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Assert that a value is within a delta range.
     *
     * This is a convenience method for integration tests that often deal
     * with floating point calculations or timing-sensitive operations.
     *
     * @param float  $expected The expected value
     * @param float  $actual   The actual value
     * @param float  $delta    The acceptable delta
     * @param string $message  Optional assertion message
     */
    protected function assertWithinDelta(float $expected, float $actual, float $delta = 0.001, string $message = ''): void
    {
        $this->assertEqualsWithDelta($expected, $actual, $delta, $message);
    }

    /**
     * Skip test if a required extension is not loaded.
     *
     * @param string $extension The required PHP extension
     * @param string $message   Optional skip message
     */
    protected function requireExtension(string $extension, string $message = ''): void
    {
        if (!extension_loaded($extension)) {
            $message = $message ?: "Test requires {$extension} extension";
            $this->markTestSkipped($message);
        }
    }

    /**
     * Skip test if running on a specific operating system.
     *
     * @param string $os      The OS to skip on (e.g., 'Windows', 'Linux', 'Darwin')
     * @param string $message Optional skip message
     */
    protected function skipOnOs(string $os, string $message = ''): void
    {
        if (PHP_OS_FAMILY === $os) {
            $message = $message ?: "Test skipped on {$os}";
            $this->markTestSkipped($message);
        }
    }
}
