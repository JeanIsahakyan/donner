# Donner Documentation

**Donner** is a lightweight PHP framework designed to simplify web application development by providing tools for dynamic routing, request validation, structured responses, and file upload handling. It streamlines the process of building web applications and APIs by handling common tasks such as URI pattern matching, parameter validation, response formatting, and error handling, with support for PHP 8.1+ features like enums.

---

## What is Donner?

**Donner** is a minimal PHP framework that aids in developing web applications and APIs by providing:
- **Dynamic Routing with Regex**: Maps HTTP requests to controllers using URI patterns with placeholders (e.g., `/user/{id}`).
- **Request Validation**: Validates and sanitizes input parameters and file uploads with type-safe methods.
- **Structured Responses**: Ensures consistent response formats, including JSON, redirects, and paginated data.
- **Error Handling**: Simplifies error reporting with custom exceptions and HTTP status codes.
- **File Upload Management**: Handles file uploads with validation for size, type, and automatic cleanup.
- **Enum Support**: Utilizes PHP 8.1 enums for robust HTTP status code definitions.

### Purpose and Use Cases

Donner is ideal for:
- Developers building lightweight web applications or APIs.
- Projects requiring dynamic routing with regex-based URI patterns.
- Applications needing robust input validation for query parameters and file uploads.
- APIs that demand consistent response formats (e.g., JSON, paginated lists, redirects).
- Projects leveraging PHP 8.1+ features like enums for type safety.
- Small to medium-sized applications where simplicity and flexibility are key.

---

## Getting Started with Donner

### Installation via Composer

Ensure your PHP version is **8.1** or higher and the `ext-json` extension is enabled.

Install Donner via Composer by adding it to your `composer.json` or running:

```bash
composer require ji/donner
```

### Recommended Project Structure

Organize your project as follows:

```*
project/
├── composer.json
├── vendor/
│   └── autoload.php
├── index.php
└── src/
    ├── Controllers/
    │   ├── HomeController.php
    │   ├── UserController.php
    │   └── NotFoundController.php
    ├── Responses/
    │   └── CustomResponse.php
    ├── Router.php
    └── (Other application files)
```

### Creating the Router

The router handles incoming requests and routes them to the appropriate controllers. Place it in `src/Router.php`.

#### Example ```Router.php```:

```php
<?php
// src/Router.php

namespace App;

use Donner\BasicRouter;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\NotFoundController;

class Router {
    public function handleRequest(): void {
        // Set headers
        header('Content-Type: application/json; charset=UTF-8');

        try {
            // Create router and add controllers
            $router = BasicRouter::create()
                ->addController(new HomeController())
                ->addController(new UserController())
                ->setNotFoundController(new NotFoundController());

            // Run the router
            $router->run();
        } catch (\Exception $exception) {
            // Handle exceptions and output error response
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

#### Explanation:

-   **Headers**: Sets JSON content type for API responses.
    
-   **Router Setup**: Instantiates BasicRouter, registers controllers, and sets a custom 404 handler.
    
-   **Error Handling**: Catches exceptions and returns a structured error response.
    

Then, in your ```index.php```, instantiate and use the router:

#### Example ```index.php```:
```php
<?php
require_once 'vendor/autoload.php';

use App\Router;

$router = new Router();
$router->handleRequest();
```
### Creating a New Controller

Controllers extend ```AbstractController``` and define routes using the ```URI``` constant. Each controller’s ```resolve``` method processes the request and returns a response.

### Defining the Controller Class

Create a new class in ```src/Controllers/```.

#### Example:
```php
<?php
// src/Controllers/UserController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;

class UserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0]; // Extracted from {id}
        return new MixedResponse(['user_id' => $userId], HTTPCode::OK);
    }
}
```
### Implementing Controller Methods

Define the ```resolve``` method to handle the request logic. The URI pattern (e.g., ```/user/{id}```) is matched using regex, and parameters are available in ```$this->params```.

#### Controller: ```HomeController```

#### Definition:
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
        return new MixedResponse(['message' => 'Welcome to Donner!'], HTTPCode::OK);
    }
}
```
#### Request Example:

```http
GET / HTTP/1.1
Host: example.com
```

#### Response Example:
```json
{
    "message": "Welcome to Donner!"
}
```

#### Controller: ```UserController```

#### Definition:

```php
<?php
// src/Controllers/UserController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use Donner\Exception\DonnerException;

class UserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        if (!is_numeric($userId)) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'User ID must be numeric',
                HTTPCode::BAD_REQUEST
            );
        }
        return new MixedResponse(['user_id' => (int)$userId], HTTPCode::OK);
    }
}
```

#### Request Example:
```http
GET /user/123 HTTP/1.1
Host: example.com
```
#### Response Example:
```json
{
    "user_id": 123
}
```
#### Error Response Example:
```http
GET /user/abc HTTP/1.1
Host: example.com
```
```json
{
    "error": {
        "error_code": 0,
        "error_message": "User ID must be numeric"
    }
}
```

#### Controller: ```UploadController```

#### Definition:
```php
<?php
// src/Controllers/UploadController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\SuccessResponse;
use Donner\Exception\DonnerException;
use Donner\Utils\HTTPCode;

class UploadController extends AbstractController {
    public const URI = '/upload';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $file = $this->request->getFile('photo')
            ->required('Photo is required')
            ->maxSize(2 * 1024 * 1024, 'Photo must be under 2MB')
            ->file();

        if ($file && $file->isImage()) {
            return new SuccessResponse();
        }
        throw new DonnerException(
            DonnerException::INVALID_REQUEST,
            'Invalid photo',
            HTTPCode::BAD_REQUEST
        );
    }
}
```

#### Request Example:
```http
POST /upload HTTP/1.1
Host: example.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="photo"; filename="image.jpg"
Content-Type: image/jpeg

(binary image data)
------WebKitFormBoundary--
```

#### Response Example:
```json
{
    "success": true
}
```

## Creating a New Response

Responses extend ```AbstractResponse``` and define the structure of data returned to the client. Public properties are automatically serialized to JSON.

#### Example:
```php
<?php
// src/Responses/UserResponse.php

namespace App\Responses;

use Donner\Response\AbstractResponse;
use Donner\Utils\HTTPCode;

class UserResponse extends AbstractResponse {
    public int $id;
    public string $name;
    public string $status;

    public function __construct(int $id, string $name, string $status) {
        parent::__construct(HTTPCode::OK);
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
    }

    public static function create(int $id, string $name, string $status): self {
        return new self($id, $name, $status);
    }
}
```
#### Usage in Controller:
```php
<?php
// src/Controllers/UserResponseController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Utils\HTTPCode;
use App\Responses\UserResponse;

class UserResponseController extends AbstractController {
    public const URI = '/user-response/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        return UserResponse::create((int)$userId, 'Jane Doe', 'active');
    }
}
```
#### Request Example:
```http
GET /user-response/123 HTTP/1.1
Host: example.com
```
#### Response Example:
```json
{
    "id": 123,
    "name": "Jane Doe",
    "status": "active"
}
```
## Using Enums

Enums define a set of named constants, used in Donner for HTTP status codes via the ```HTTPCode``` enum.

### Defining an Enum
#### Example:
```php
<?php
// src/Enums/UserStatus.php

namespace App\Enums;

enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}
```

### Using Enums in Controllers
#### Example:
```php
<?php
// src/Controllers/StatusController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use App\Enums\UserStatus;
use Donner\Exception\DonnerException;

class StatusController extends AbstractController {
    public const URI = '/status/{id}';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        $status = $this->request->get('status')
            ->enum(
                array_map(fn($case) => $case->value, UserStatus::cases()),
                'Invalid status'
            );
        return new MixedResponse([
            'user_id' => $userId,
            'status' => $status
        ], HTTPCode::OK);
    }
}
```
#### Request Example:
```http
POST /status/123 HTTP/1.1
Host: example.com
Content-Type: application/x-www-form-urlencoded

status=active
```
#### Response Example:
```json
{
    "user_id": "123",
    "status": "active"
}
```
## Handling Requests
Requests are handled by ```BasicRouter```, which matches the request URI and HTTP method to a controller’s ```resolve``` method.
##### Example in Router:
```php
$router = BasicRouter::create()
    ->addController(new UserController())
    ->run();
```
## Error Handling
### Throwing Errors
Use ```DonnerException``` to throw errors with custom codes, messages, and HTTP status codes.
#### Example:
```php
if (!is_numeric($userId)) {
    throw new DonnerException(
        DonnerException::INVALID_REQUEST,
        'User ID must be numeric',
        HTTPCode::BAD_REQUEST
    );
}
```
#### Common Error Codes:
 - ```DonnerException::INVALID_REQUEST``` (0): Generic invalid request error.

### Handling Errors in the Router
Errors are caught in ```Router.php``` and returned as structured JSON responses.

#### Example Error Response:
```json
{
    "error": {
        "error_code": 0,
        "error_message": "User ID must be numeric"
    }
}
```

