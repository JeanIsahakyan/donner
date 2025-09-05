# Donner Framework Documentation

Welcome to the comprehensive documentation for the Donner PHP framework. This documentation covers everything you need to know to build web applications and APIs with Donner.

## Table of Contents

- [Getting Started](getting-started.md)
- [Installation Guide](installation.md)
- [Architecture Overview](architecture.md)
- [API Reference](api-reference.md)
- [Examples & Tutorials](examples.md)
- [Best Practices](best-practices.md)
- [Migration Guide](migration.md)
- [Contributing](contributing.md)

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use Donner\BasicRouter;
use App\Controllers\HomeController;

BasicRouter::create()
    ->addController(new HomeController())
    ->run();
```

## What is Donner?

Donner is a lightweight, modern PHP framework designed for building web applications and APIs. It provides:

- **Dynamic Routing** with regex-based URI patterns
- **Request Validation** with type-safe parameter handling
- **Structured Responses** for consistent API output
- **File Upload Management** with validation and cleanup
- **Modern PHP 8.1+** features including enums
- **Minimal Dependencies** for fast performance

## Key Features

### 🚀 **Fast & Lightweight**
- Minimal overhead with only essential dependencies
- Optimized for performance and speed
- Small memory footprint

### 🛡️ **Type Safety**
- PHP 8.1+ enum support for HTTP status codes
- Type-safe request parameter validation
- Reflection-based response serialization

### 🔧 **Developer Friendly**
- Fluent API for parameter validation
- Clear error messages and debugging
- Intuitive controller structure

### 📁 **File Handling**
- Secure file upload validation
- Automatic cleanup and memory management
- MIME type checking and size limits

## Requirements

- PHP 8.1 or higher
- `ext-json` extension
- Composer (for installation)

## Installation

```bash
composer require ji/donner
```

## Documentation Structure

### Getting Started
Learn the basics of Donner with step-by-step tutorials and examples.

### API Reference
Complete reference for all classes, methods, and interfaces in the framework.

### Examples
Real-world examples and common use cases for building applications.

### Architecture
Deep dive into the framework's design patterns and internal structure.

## Support

- **Issues**: [GitHub Issues](https://github.com/your-repo/donner/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-repo/donner/discussions)
- **Email**: jeanisahakyan@gmail.com

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

*Last updated: December 2024*
