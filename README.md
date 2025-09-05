# Donner Framework

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/composer-ji%2Fdonner-blue.svg)](https://packagist.org/packages/ji/donner)

**Donner** is a lightweight, modern PHP framework designed for building web applications and APIs. It provides a clean, simple API for routing, request validation, response handling, and file uploads with support for PHP 8.1+ features.

## ✨ Features

- 🚀 **Lightweight & Fast** - Minimal overhead with only essential dependencies
- 🛡️ **Type Safety** - PHP 8.1+ enum support and type-safe validation
- 🔧 **Developer Friendly** - Fluent API and intuitive controller structure
- 📁 **File Handling** - Secure file upload validation and management
- 🎯 **Dynamic Routing** - Regex-based URI patterns with parameter extraction
- 📊 **Structured Responses** - Consistent JSON responses with pagination support
- ⚡ **Modern PHP** - Built for PHP 8.1+ with enums and modern features

## 🚀 Quick Start

### Installation

```bash
composer require ji/donner
```

### Basic Example

```php
<?php
require_once 'vendor/autoload.php';

use Donner\BasicRouter;
use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;

class HomeController extends AbstractController {
    public const URI = '/';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        return new MixedResponse([
            'message' => 'Welcome to Donner!',
            'version' => '1.0.0'
        ], HTTPCode::OK);
    }
}

BasicRouter::create()
    ->addController(new HomeController())
    ->run();
```

### Request Validation

```php
class UserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        
        // Validate user ID
        if (!is_numeric($userId)) {
            throw new \Donner\Exception\DonnerException(
                \Donner\Exception\DonnerException::INVALID_REQUEST,
                'Invalid user ID',
                HTTPCode::BAD_REQUEST
            );
        }

        return new MixedResponse(['user_id' => (int)$userId], HTTPCode::OK);
    }
}
```

### POST with Validation

```php
class CreateUserController extends AbstractController {
    public const URI = '/users';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $name = $this->request->get('name')
            ->required('Name is required')
            ->string('Name must be a string');

        $email = $this->request->get('email')
            ->required('Email is required')
            ->string('Email must be a string');

        $age = $this->request->get('age')
            ->required('Age is required')
            ->int('Age must be a number')
            ->positive('Age must be positive');

        // Process user creation...
        return new \Donner\Response\SuccessResponse();
    }
}
```

## 📚 Documentation

### Getting Started
- [Installation Guide](docs/installation.md) - Complete setup instructions
- [Getting Started](docs/getting-started.md) - Quick start tutorial
- [Examples & Tutorials](docs/examples.md) - Comprehensive examples

### Reference
- [API Reference](docs/api-reference.md) - Complete API documentation
- [Architecture Overview](docs/architecture.md) - Framework architecture
- [Best Practices](docs/best-practices.md) - Recommended patterns

## 🏗️ Architecture

Donner follows a clean, layered architecture:

```
Request → Router → Controller → Response
   ↓        ↓         ↓          ↓
Web Server → Pattern → Business → JSON
           Matching   Logic     Output
```

### Core Components

- **Router** - Handles request routing and parameter extraction
- **Controllers** - Process business logic and return responses
- **Request Validation** - Type-safe parameter validation
- **Response System** - Structured response formatting
- **File Upload** - Secure file handling with validation

## 🎯 Use Cases

Donner is perfect for:

- **APIs** - RESTful APIs with JSON responses
- **Web Applications** - Lightweight web apps
- **Microservices** - Small, focused services
- **Prototypes** - Rapid prototyping and development
- **Learning** - Understanding modern PHP frameworks

## 🔧 Requirements

- PHP 8.1 or higher
- `ext-json` extension
- Composer (for installation)

## 📦 Installation

### Via Composer

```bash
composer require ji/donner
```

### Manual Installation

1. Download the latest release
2. Extract to your project directory
3. Include the autoloader:

```php
require_once 'vendor/autoload.php';
```

## 🚀 Quick Examples

### Simple API Endpoint

```php
class StatusController extends AbstractController {
    public const URI = '/status';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        return new MixedResponse([
            'status' => 'online',
            'timestamp' => time()
        ], HTTPCode::OK);
    }
}
```

### File Upload

```php
class UploadController extends AbstractController {
    public const URI = '/upload';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $file = $this->request->getFile('photo')
            ->required('Photo is required')
            ->maxSize(5 * 1024 * 1024, 'File must be under 5MB')
            ->file();

        if ($file && $file->isImage()) {
            return new \Donner\Response\SuccessResponse();
        }

        throw new \Donner\Exception\DonnerException(
            \Donner\Exception\DonnerException::INVALID_REQUEST,
            'Invalid image file',
            HTTPCode::BAD_REQUEST
        );
    }
}
```

### Paginated Response

```php
class UsersController extends AbstractController {
    public const URI = '/users';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $page = $this->request->get('page')->defaultValue(1)->int();
        $limit = $this->request->get('limit')->defaultValue(10)->int()->positive();

        $users = $this->getUsers($page, $limit);
        $totalCount = $this->getTotalCount();

        return (new \Donner\Response\ItemsResponse())
            ->setItems($users)
            ->setTotalCount($totalCount)
            ->setNextCursor($page * $limit < $totalCount ? $page + 1 : null)
            ->setPreviousCursor($page > 1 ? $page - 1 : null);
    }
}
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](docs/contributing.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built with modern PHP 8.1+ features
- Inspired by clean, simple API design
- Community feedback and contributions

## 📞 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](https://github.com/your-repo/donner/issues)
- **Email**: jeanisahakyan@gmail.com

---

**Made with ❤️ for the PHP community**