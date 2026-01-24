<?php

declare(strict_types=1);

/**
 * Setup script for PHP Library Template
 * 
 * This script helps users customize the template by replacing placeholders
 * with their actual project information.
 */

class TemplateSetup
{
    private array $replacements = [];
    private array $filesToProcess = [
        'composer.json',
        'README.md',
        'CHANGELOG.md',
        'CONTRIBUTING.md',
        'SECURITY.md',
        'src/Calculator.php',
        'tests/Unit/CalculatorTest.php',
        'tests/Integration/IntegrationTestCase.php',
        'tests/Integration/CalculatorIntegrationTest.php',
    ];

    public function run(): void
    {
        echo "🚀 PHP Library Template Setup\n";
        echo "=============================\n\n";

        $this->collectUserInput();
        $this->processFiles();
        $this->showCompletion();
    }

    private function collectUserInput(): void
    {
        // Get vendor name
        $vendor = $this->prompt('Enter your vendor name (e.g., "acme"): ');
        $vendor = strtolower(trim($vendor));
        
        if (empty($vendor)) {
            echo "❌ Vendor name cannot be empty.\n";
            exit(1);
        }

        // Get package name
        $package = $this->prompt('Enter your package name (e.g., "awesome-library"): ');
        $package = strtolower(trim($package));
        
        if (empty($package)) {
            echo "❌ Package name cannot be empty.\n";
            exit(1);
        }

        // Get author name
        $authorName = $this->prompt('Enter your name: ');
        $authorName = trim($authorName);
        
        if (empty($authorName)) {
            echo "❌ Author name cannot be empty.\n";
            exit(1);
        }

        // Get author email
        $authorEmail = $this->prompt('Enter your email: ');
        $authorEmail = trim($authorEmail);
        
        if (empty($authorEmail) || !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
            echo "❌ Please enter a valid email address.\n";
            exit(1);
        }

        // Get GitHub username
        $githubUsername = $this->prompt('Enter your GitHub username: ');
        $githubUsername = trim($githubUsername);
        
        if (empty($githubUsername)) {
            echo "❌ GitHub username cannot be empty.\n";
            exit(1);
        }

        // Get repository name (default to package name)
        $repoName = $this->prompt("Enter your repository name [{$package}]: ");
        $repoName = trim($repoName) ?: $package;

        // Get package description
        $description = $this->prompt('Enter a brief description of your package: ');
        $description = trim($description);
        
        if (empty($description)) {
            echo "❌ Package description cannot be empty.\n";
            exit(1);
        }

        // Convert to proper cases
        $vendorNamespace = $this->toPascalCase($vendor);
        $packageNamespace = $this->toPascalCase($package);

        // Store replacements
        $this->replacements = [
            'your-vendor' => $vendor,
            'your-package' => $package,
            'YourVendor' => $vendorNamespace,
            'YourPackage' => $packageNamespace,
            'Your Name' => $authorName,
            'your-email@example.com' => $authorEmail,
            'your-username' => $githubUsername,
            'your-repo' => $repoName,
            'A brief description of your package' => $description,
        ];

        echo "\n📋 Configuration Summary:\n";
        echo "========================\n";
        echo "Vendor: {$vendor}\n";
        echo "Package: {$package}\n";
        echo "Namespace: {$vendorNamespace}\\{$packageNamespace}\n";
        echo "Author: {$authorName} <{$authorEmail}>\n";
        echo "GitHub: {$githubUsername}/{$repoName}\n";
        echo "Description: {$description}\n\n";

        $confirm = $this->prompt('Is this correct? (y/N): ');
        if (strtolower(trim($confirm)) !== 'y') {
            echo "❌ Setup cancelled.\n";
            exit(1);
        }
    }

    private function processFiles(): void
    {
        echo "🔄 Processing files...\n";

        foreach ($this->filesToProcess as $file) {
            if (!file_exists($file)) {
                echo "⚠️  Skipping {$file} (not found)\n";
                continue;
            }

            $content = file_get_contents($file);
            $originalContent = $content;

            foreach ($this->replacements as $search => $replace) {
                $content = str_replace($search, $replace, $content);
            }

            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "✅ Updated {$file}\n";
            } else {
                echo "➡️  No changes needed for {$file}\n";
            }
        }
    }

    private function removeSetupScript(): void
    {
        $composerFile = 'composer.json';
        if (!file_exists($composerFile)) {
            echo "⚠️  composer.json not found, skipping setup script removal\n";
            return;
        }

        $content = file_get_contents($composerFile);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "⚠️  Could not parse composer.json, skipping setup script removal\n";
            return;
        }

        if (isset($data['scripts']['setup'])) {
            unset($data['scripts']['setup']);

            $newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($composerFile, $newContent . "\n");

            echo "✅ Removed setup script from composer.json\n";
        }
    }

    private function cleanupReadmeComments(): void
    {
        $readmeFile = 'README.md';
        if (!file_exists($readmeFile)) {
            echo "⚠️  README.md not found, skipping comment cleanup\n";
            return;
        }

        $content = file_get_contents($readmeFile);

        // Remove the template setup comment block
        $pattern = '/<!--\s*TEMPLATE SETUP:.*?-->\s*\n?/s';
        $cleanContent = preg_replace($pattern, '', $content);

        if ($cleanContent !== $content) {
            file_put_contents($readmeFile, $cleanContent);
            echo "✅ Removed template comments from README.md\n";
        }
    }

    private function showCompletion(): void
    {
        $this->removeSetupScript();
        $this->cleanupReadmeComments();

        echo "\n🎉 Setup Complete!\n";
        echo "==================\n\n";
        echo "Your PHP library template has been customized successfully!\n\n";
        echo "Next steps:\n";
        echo "1. Review and update the README.md file\n";
        echo "2. Replace the Calculator example with your actual code\n";
        echo "3. Update tests to match your functionality\n";
        echo "4. Run 'composer install' to ensure everything works\n";
        echo "5. Run 'composer test' to verify tests pass\n";
        echo "6. Initialize your git repository if not already done\n";
        echo "7. Delete setup.php file (no longer needed)\n\n";
        echo "Cleanup completed:\n";
        echo "• 'composer setup' command removed from composer.json\n";
        echo "• Template setup comments removed from README.md\n\n";
        echo "Happy coding! 🚀\n";
    }

    private function prompt(string $message): string
    {
        echo $message;
        return trim(fgets(STDIN) ?: '');
    }

    private function toPascalCase(string $string): string
    {
        // Convert kebab-case, snake_case, or space-separated to PascalCase
        $string = preg_replace('/[-_\s]+/', ' ', $string);
        $string = ucwords(strtolower($string));
        return str_replace(' ', '', $string);
    }
}

// Run the setup if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $setup = new TemplateSetup();
    $setup->run();
}
