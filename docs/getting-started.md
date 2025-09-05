# Getting Started with Donner

This guide will help you get up and running with the Donner framework quickly.

## Prerequisites

Before you begin, ensure you have:

- PHP 8.1 or higher installed
- Composer package manager
- A web server (Apache, Nginx, or PHP built-in server)
- Basic knowledge of PHP and object-oriented programming

## Installation

### Step 1: Install via Composer

Create a new project directory and install Donner:

```bash
mkdir my-donner-app
cd my-donner-app
composer require ji/donner
```

### Step 2: Create Your First Controller

Create a `src/Controllers` directory and add your first controller:

```php
<?php
// src/Controllers/HomeController.php

namespace App\Controllers;

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
```

### Step 3: Create the Router

Create a `src/Router.php` file:

```php
<?php
// src/Router.php

namespace App;

use Donner\BasicRouter;
use App\Controllers\HomeController;

class Router {
    public function handleRequest(): void {
        // Set JSON content type for API responses
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $router = BasicRouter::create()
                ->addController(new HomeController())
                ->run();
        } catch (\Exception $exception) {
            // Handle exceptions
            http_response_code(500);
            echo json_encode([
                'error' => [
                    'error_code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                ]
            ]);
        }
    }
}
```

### Step 4: Create the Entry Point

Create an `index.php` file in your project root:

```php
<?php
// index.php

require_once 'vendor/autoload.php';

use App\Router;

$router = new Router();
$router->handleRequest();
```

### Step 5: Test Your Application

Start the PHP built-in server:

```bash
php -S localhost:8000
```

Visit `http://localhost:8000` in your browser. You should see:

```json
{
    "message": "Welcome to Donner!",
    "version": "1.0.0"
}
```

## Understanding the Basics

### Controllers

Controllers in Donner extend `AbstractController` and must define:

- `URI`: The route pattern (e.g., `/user/{id}`)
- `ALLOWED_METHOD`: HTTP method (GET, POST, or ALL)
- `resolve()`: Method that handles the request and returns a response

### Routes

Routes use a simple pattern syntax:
- `/` - Exact match
- `/user/{id}` - Parameter capture
- `/api/v1/{resource}` - Nested parameters

### Responses

Donner provides several response types:
- `MixedResponse`: For any data type
- `SuccessResponse`: Simple success indicator
- `ItemsResponse`: Paginated lists
- `RedirectResponse`: HTTP redirects

### Request Parameters

Access request data through `$this->request`:

```php
public function resolve(): ResponseInterface {
    $name = $this->request->get('name')->string();
    $age = $this->request->get('age')->int();
    
    return new MixedResponse([
        'name' => $name,
        'age' => $age
    ]);
}
```

## Next Steps

Now that you have a basic Donner application running:

1. **Add More Controllers**: Create additional controllers for different endpoints
2. **Handle Different HTTP Methods**: Add POST, PUT, DELETE controllers
3. **Validate Input**: Use Donner's validation methods
4. **Handle File Uploads**: Learn about file upload handling
5. **Create Custom Responses**: Build your own response classes

Continue with the [Examples & Tutorials](examples.md) for more detailed examples, or check the [API Reference](api-reference.md) for complete documentation.

## Common Issues

### "Class not found" errors
Make sure you're using Composer's autoloader and have the correct namespace declarations.

### Routes not matching
Check that your URI patterns are correct and that the HTTP method matches your controller's `ALLOWED_METHOD`.

### JSON responses not working
Ensure you're setting the correct `Content-Type` header in your router.

## Getting Help

- Check the [API Reference](api-reference.md) for detailed documentation
- Look at [Examples](examples.md) for common use cases
- Review [Best Practices](best-practices.md) for recommended patterns
- Open an issue on GitHub if you encounter bugs
