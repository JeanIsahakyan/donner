# Contributing to Donner

Thank you for your interest in contributing to the Donner framework! This guide will help you get started with contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Contributing Guidelines](#contributing-guidelines)
- [Pull Request Process](#pull-request-process)
- [Issue Reporting](#issue-reporting)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Documentation](#documentation)

## Code of Conduct

This project follows a code of conduct that we expect all contributors to follow. Please be respectful, inclusive, and constructive in all interactions.

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Git
- A text editor or IDE

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork locally:

```bash
git clone https://github.com/your-username/donner.git
cd donner
```

3. Add the upstream remote:

```bash
git remote add upstream https://github.com/original-owner/donner.git
```

## Development Setup

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
composer test
```

### Check Code Style

```bash
composer cs-check
```

### Fix Code Style

```bash
composer cs-fix
```

## Contributing Guidelines

### Types of Contributions

We welcome several types of contributions:

- **Bug Fixes** - Fix existing issues
- **New Features** - Add new functionality
- **Documentation** - Improve or add documentation
- **Tests** - Add or improve test coverage
- **Performance** - Optimize existing code
- **Examples** - Add usage examples

### Before You Start

1. Check existing issues and pull requests
2. Discuss major changes in an issue first
3. Ensure your changes align with the project goals
4. Follow the coding standards

## Pull Request Process

### Creating a Pull Request

1. **Create a branch** for your changes:

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/issue-number
```

2. **Make your changes** following the coding standards

3. **Write tests** for your changes

4. **Update documentation** if needed

5. **Commit your changes** with a clear message:

```bash
git commit -m "Add feature: brief description"
```

6. **Push to your fork**:

```bash
git push origin feature/your-feature-name
```

7. **Create a Pull Request** on GitHub

### Pull Request Guidelines

- **Title**: Use a clear, descriptive title
- **Description**: Explain what changes you made and why
- **Reference Issues**: Link to related issues using `#issue-number`
- **Screenshots**: Include screenshots for UI changes
- **Breaking Changes**: Clearly mark any breaking changes

### Example Pull Request

```markdown
## Description
Add support for custom response headers in controllers.

## Changes
- Added `setHeader()` method to AbstractController
- Updated response rendering to include custom headers
- Added tests for header functionality

## Related Issues
Fixes #123

## Breaking Changes
None

## Testing
- [ ] All existing tests pass
- [ ] New tests added for header functionality
- [ ] Manual testing completed
```

## Issue Reporting

### Before Creating an Issue

1. Search existing issues to avoid duplicates
2. Check if the issue is already fixed in the latest version
3. Gather relevant information about the issue

### Issue Template

When creating an issue, please include:

```markdown
## Bug Report / Feature Request

### Description
Brief description of the issue or feature request.

### Steps to Reproduce (for bugs)
1. Step one
2. Step two
3. Step three

### Expected Behavior
What you expected to happen.

### Actual Behavior
What actually happened.

### Environment
- PHP Version: 8.1.x
- Donner Version: 1.0.0
- OS: Ubuntu 20.04
- Web Server: Apache 2.4

### Additional Context
Any other relevant information.
```

## Coding Standards

### PHP Standards

We follow PSR-12 coding standards:

- Use 4 spaces for indentation
- Use camelCase for methods and variables
- Use PascalCase for classes
- Use UPPER_CASE for constants
- Add type hints where possible
- Add docblocks for public methods

### Code Style Example

```php
<?php

namespace Donner\Controller;

use Donner\Response\ResponseInterface;
use Donner\Utils\HTTPCode;

/**
 * Example controller demonstrating coding standards
 */
class ExampleController extends AbstractController
{
    public const URI = '/example';
    public const ALLOWED_METHOD = self::METHOD_GET;

    private string $exampleProperty;

    public function __construct(string $exampleProperty = 'default')
    {
        parent::__construct();
        $this->exampleProperty = $exampleProperty;
    }

    /**
     * Resolves the request and returns a response
     *
     * @return ResponseInterface
     */
    public function resolve(): ResponseInterface
    {
        return new MixedResponse([
            'message' => 'Example response',
            'property' => $this->exampleProperty
        ], HTTPCode::OK);
    }
}
```

### Naming Conventions

- **Classes**: PascalCase (`UserController`)
- **Methods**: camelCase (`getUserById`)
- **Variables**: camelCase (`$userId`)
- **Constants**: UPPER_CASE (`METHOD_GET`)
- **Files**: PascalCase (`UserController.php`)

## Testing

### Writing Tests

All new features and bug fixes should include tests:

```php
<?php

namespace Tests\Controller;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use PHPUnit\Framework\TestCase;

class ExampleControllerTest extends TestCase
{
    public function testResolveReturnsValidResponse(): void
    {
        $controller = new ExampleController();
        $response = $controller->resolve();

        $this->assertInstanceOf(MixedResponse::class, $response);
        $this->assertEquals(HTTPCode::OK, $response->getHTTPCode());
    }
}
```

### Test Categories

- **Unit Tests** - Test individual components
- **Integration Tests** - Test component interactions
- **Feature Tests** - Test complete features
- **Performance Tests** - Test performance characteristics

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
vendor/bin/phpunit tests/Controller/ExampleControllerTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

## Documentation

### Documentation Standards

- Use clear, concise language
- Include code examples
- Update documentation with code changes
- Follow the existing documentation style

### Types of Documentation

- **API Documentation** - Method and class documentation
- **User Guides** - How-to guides and tutorials
- **Architecture Docs** - Technical architecture information
- **Examples** - Code examples and use cases

### Documentation Updates

When adding new features:

1. Update the API reference
2. Add usage examples
3. Update the getting started guide if needed
4. Add to the examples section

## Release Process

### Version Numbering

We use [Semantic Versioning](https://semver.org/):

- **MAJOR** - Breaking changes
- **MINOR** - New features (backward compatible)
- **PATCH** - Bug fixes (backward compatible)

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] Changelog updated
- [ ] Version number bumped
- [ ] Tag created
- [ ] Release notes written

## Community

### Getting Help

- **GitHub Discussions** - Ask questions and discuss ideas
- **GitHub Issues** - Report bugs and request features
- **Email** - jeanisahakyan@gmail.com

### Recognition

Contributors will be recognized in:
- CONTRIBUTORS.md file
- Release notes
- Project documentation

## License

By contributing to Donner, you agree that your contributions will be licensed under the MIT License.

## Questions?

If you have any questions about contributing, please:

1. Check this guide first
2. Search existing issues
3. Create a new issue
4. Contact the maintainers

Thank you for contributing to Donner! 🎉
