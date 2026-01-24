<?php

declare(strict_types=1);

/**
 * Remove Example Script for PHP Library Template
 * 
 * This script removes the Calculator example code and tests,
 * providing a clean slate for users to implement their own functionality.
 */

class ExampleRemover
{
    private array $filesToRemove = [
        'src/Calculator.php',
        'tests/Unit/CalculatorTest.php',
        'tests/Integration/CalculatorIntegrationTest.php',
    ];

    public function run(): void
    {
        echo "🧹 PHP Library Template - Remove Example Code\n";
        echo "=============================================\n\n";

        $this->confirmRemoval();
        $this->removeFiles();
        $this->createPlaceholderFiles();
        $this->removeScriptFromComposer();
        $this->showCompletion();
    }

    private function confirmRemoval(): void
    {
        echo "This will remove the following example files:\n";
        foreach ($this->filesToRemove as $file) {
            if (file_exists($file)) {
                echo "• {$file}\n";
            }
        }
        
        echo "\nThis action cannot be undone!\n\n";
        
        $confirm = $this->prompt('Are you sure you want to remove the example code? (y/N): ');
        if (strtolower(trim($confirm)) !== 'y') {
            echo "❌ Operation cancelled.\n";
            exit(0);
        }
    }

    private function removeFiles(): void
    {
        echo "\n🗑️  Removing example files...\n";

        foreach ($this->filesToRemove as $file) {
            if (file_exists($file)) {
                if (unlink($file)) {
                    echo "✅ Removed {$file}\n";
                } else {
                    echo "❌ Failed to remove {$file}\n";
                }
            } else {
                echo "⚠️  {$file} not found (already removed?)\n";
            }
        }
    }

    private function createPlaceholderFiles(): void
    {
        echo "\n📝 Creating placeholder files...\n";

        // Create a placeholder class in src/
        $this->createPlaceholderClass();
        
        // Create placeholder test files
        $this->createPlaceholderUnitTest();
        $this->createPlaceholderIntegrationTest();
    }

    private function createPlaceholderClass(): void
    {
        $content = '<?php

declare(strict_types=1);

namespace YourVendor\YourPackage;

/**
 * Example class - replace with your actual implementation
 */
class ExampleClass
{
    public function __construct()
    {
        // Your implementation here
    }

    public function exampleMethod(): string
    {
        return "Hello from your library!";
    }
}
';

        if (file_put_contents('src/ExampleClass.php', $content)) {
            echo "✅ Created src/ExampleClass.php\n";
        } else {
            echo "❌ Failed to create src/ExampleClass.php\n";
        }
    }

    private function createPlaceholderUnitTest(): void
    {
        $content = '<?php

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
';

        if (file_put_contents('tests/Unit/ExampleClassTest.php', $content)) {
            echo "✅ Created tests/Unit/ExampleClassTest.php\n";
        } else {
            echo "❌ Failed to create tests/Unit/ExampleClassTest.php\n";
        }
    }

    private function createPlaceholderIntegrationTest(): void
    {
        $content = '<?php

declare(strict_types=1);

namespace YourVendor\YourPackage\Tests\Integration;

use YourVendor\YourPackage\ExampleClass;

/**
 * Integration tests for ExampleClass - replace with your actual integration tests
 */
class ExampleClassIntegrationTest extends IntegrationTestCase
{
    private ExampleClass $exampleClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exampleClass = new ExampleClass();
    }

    public function testExampleIntegration(): void
    {
        // Example integration test
        $result = $this->exampleClass->exampleMethod();
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testExampleWithExternalDependency(): void
    {
        // Example test that might involve external dependencies
        // Replace with your actual integration scenarios
        
        $this->assertTrue(true, "Replace with actual integration test");
    }
}
';

        if (file_put_contents('tests/Integration/ExampleClassIntegrationTest.php', $content)) {
            echo "✅ Created tests/Integration/ExampleClassIntegrationTest.php\n";
        } else {
            echo "❌ Failed to create tests/Integration/ExampleClassIntegrationTest.php\n";
        }
    }

    private function removeScriptFromComposer(): void
    {
        $composerFile = 'composer.json';
        if (!file_exists($composerFile)) {
            echo "⚠️  composer.json not found, skipping script removal\n";
            return;
        }
        
        $content = file_get_contents($composerFile);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "⚠️  Could not parse composer.json, skipping script removal\n";
            return;
        }
        
        if (isset($data['scripts']['remove-example'])) {
            unset($data['scripts']['remove-example']);
            
            $newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($composerFile, $newContent . "\n");
            
            echo "✅ Removed remove-example script from composer.json\n";
        }
    }

    private function showCompletion(): void
    {
        echo "\n🎉 Example Code Removal Complete!\n";
        echo "==================================\n\n";
        echo "The Calculator example code has been removed and replaced with placeholder files.\n\n";
        echo "What was done:\n";
        echo "• Removed Calculator.php and its tests\n";
        echo "• Created ExampleClass.php with basic structure\n";
        echo "• Created placeholder unit and integration tests\n";
        echo "• Removed 'composer remove-example' command\n\n";
        echo "Next steps:\n";
        echo "1. Replace ExampleClass with your actual implementation\n";
        echo "2. Update the placeholder tests with your actual test cases\n";
        echo "3. Run 'composer test' to verify your tests pass\n";
        echo "4. Delete remove-example.php file (no longer needed)\n\n";
        echo "You now have a clean slate to build your library! 🚀\n";
    }

    private function prompt(string $message): string
    {
        echo $message;
        return trim(fgets(STDIN) ?: '');
    }
}

// Run the remover if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $remover = new ExampleRemover();
    $remover->run();
}
