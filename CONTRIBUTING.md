# Contributing

Thank you for considering contributing to this project! We welcome contributions from everyone.

## Development Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/zestic/auth-library.git
   cd auth-library
   ```
3. Install dependencies:
   ```bash
   composer install
   ```

## Development Workflow

### Before Making Changes

1. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make sure all tests pass:
   ```bash
   composer test
   ```

### Making Changes

1. Write your code following the project's coding standards
2. Add tests for any new functionality
3. Update documentation if needed
4. Ensure all quality checks pass:
   ```bash
   composer quality
   ```

### Code Style

This project follows the PER-3 coding standard. Before submitting:

1. Run the code style fixer:
   ```bash
   composer cs-fix
   ```

2. Check for any remaining style issues:
   ```bash
   composer cs-check
   ```

### Static Analysis

Ensure your code passes static analysis:

```bash
composer phpstan
```

### Testing

- Write tests for new features and bug fixes
- Ensure all tests pass: `composer test`
- Aim for good test coverage
- Use descriptive test method names

#### Test Types

- **Unit Tests**: Test individual classes/methods in isolation
- **Integration Tests**: Test how components work together

### Submitting Changes

1. Push your branch to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

2. Create a Pull Request with:
   - Clear title and description
   - Reference any related issues
   - Include screenshots for UI changes
   - List any breaking changes

### Pull Request Guidelines

- Keep PRs focused and atomic
- Write clear commit messages
- Update CHANGELOG.md if needed
- Ensure CI passes
- Be responsive to feedback

## Reporting Issues

When reporting issues, please include:

- PHP version
- Steps to reproduce
- Expected vs actual behavior
- Any error messages
- Minimal code example if applicable

## Questions?

Feel free to open an issue for questions or reach out to the maintainers.

## Code of Conduct

Please note that this project follows a [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree to abide by its terms.
