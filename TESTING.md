# Testing Guide

This project uses PHPUnit 11 for unit and integration testing.

## Test Structure

The test suite is organized into two main categories:

- **Unit Tests** (`tests/Unit/`): Test individual classes and methods in isolation
- **Integration Tests** (`tests/Integration/`): Test how components work together and complex workflows

## Running Tests

### All Tests
```bash
# Using Composer script
composer test

# Using PHPUnit directly
./vendor/bin/phpunit
```

### Unit Tests Only
```bash
# Using Composer script
composer test:unit

# Using PHPUnit directly
./vendor/bin/phpunit --testsuite=Unit
```

### Integration Tests Only
```bash
# Using Composer script
composer test:integration

# Using PHPUnit directly
./vendor/bin/phpunit --testsuite=Integration
```

### Code Coverage
```bash
# Generate HTML coverage report
composer test:coverage

# Generate text coverage report
composer test:coverage-text

# Using PHPUnit directly (requires Xdebug)
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage/html
```

## Test Configuration

The PHPUnit configuration is defined in `phpunit.xml`:

- **Bootstrap**: Uses Composer's autoloader (`vendor/autoload.php`)
- **Test Suites**: Separate suites for Unit and Integration tests
- **Coverage**: Configured to generate HTML, text, and Clover reports
- **Logging**: JUnit XML output for CI/CD integration
- **Cache**: Uses `.phpunit.cache/` directory for improved performance

## Writing Tests

### Unit Test Example

```php
<?php

declare(strict_types=1);

namespace Zestic\PhpLibraryTemplate\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zestic\PhpLibraryTemplate\YourClass;

class YourClassTest extends TestCase
{
    private YourClass $subject;

    protected function setUp(): void
    {
        $this->subject = new YourClass();
    }

    public function testSomeMethod(): void
    {
        $result = $this->subject->someMethod('input');
        
        $this->assertEquals('expected', $result);
    }
}
```

### Integration Test Example

Integration tests should extend the `IntegrationTestCase` base class which provides common utilities:

```php
<?php

declare(strict_types=1);

namespace Zestic\PhpLibraryTemplate\Tests\Integration;

class WorkflowIntegrationTest extends IntegrationTestCase
{
    public function testComplexWorkflow(): void
    {
        // Test multiple components working together
        // May involve external dependencies, file system, etc.

        // Use helper methods from IntegrationTestCase
        $tempFile = $this->createTemporaryFile('test content');
        $tempDir = $this->createTemporaryDirectory();

        // Use convenient assertion methods
        $this->assertWithinDelta(1.0, 1.001, 0.01);

        // Skip tests based on conditions
        $this->requireExtension('curl');
        $this->skipOnOs('Windows', 'Test not supported on Windows');
    }
}
```

#### IntegrationTestCase Features

The `IntegrationTestCase` base class provides:

- **File System Helpers**: `createTemporaryFile()` and `createTemporaryDirectory()` with automatic cleanup
- **Assertion Helpers**: `assertWithinDelta()` for floating-point comparisons
- **Test Conditions**: `requireExtension()` and `skipOnOs()` for conditional test execution
- **Setup/Teardown**: Common integration test setup and cleanup hooks

## Best Practices

1. **Test Naming**: Use descriptive test method names that explain what is being tested
2. **Data Providers**: Use data providers for testing multiple scenarios
3. **Setup/Teardown**: Use `setUp()` and `tearDown()` methods for test preparation and cleanup
4. **Assertions**: Use specific assertions (`assertSame`, `assertInstanceOf`, etc.) over generic ones
5. **Exception Testing**: Use `expectException()` for testing error conditions
6. **Test Isolation**: Each test should be independent and not rely on other tests

## CI/CD Integration

The test configuration includes JUnit XML output (`coverage/junit.xml`) for integration with CI/CD systems like GitHub Actions, GitLab CI, or Jenkins.

Example GitHub Actions workflow:

```yaml
- name: Run Tests
  run: composer test

- name: Upload Coverage
  uses: codecov/codecov-action@v3
  with:
    file: ./coverage/clover.xml
```

## Coverage Reports

Coverage reports are generated in the `coverage/` directory:

- `coverage/html/` - HTML coverage report (open `index.html` in browser)
- `coverage/coverage.txt` - Text coverage summary
- `coverage/clover.xml` - Clover XML format for CI/CD tools
- `coverage/junit.xml` - JUnit XML test results

## Troubleshooting

### Xdebug Coverage Warning

If you see warnings about `XDEBUG_MODE=coverage`, install and configure Xdebug:

```bash
# Set environment variable
export XDEBUG_MODE=coverage

# Or run with the variable
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage/html
```

### Memory Issues

For large test suites, you may need to increase PHP memory limit:

```bash
php -d memory_limit=512M ./vendor/bin/phpunit
```
