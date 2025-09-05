# Examples & Tutorials

This section provides comprehensive examples and tutorials for using the Donner framework.

## Table of Contents

- [Basic Examples](#basic-examples)
- [Advanced Examples](#advanced-examples)
- [Real-world Applications](#real-world-applications)
- [Common Patterns](#common-patterns)
- [Troubleshooting Examples](#troubleshooting-examples)

## Basic Examples

### Simple API Endpoint

Create a basic API endpoint that returns JSON data:

```php
<?php
// src/Controllers/StatusController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;

class StatusController extends AbstractController {
    public const URI = '/status';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        return new MixedResponse([
            'status' => 'online',
            'timestamp' => time(),
            'version' => '1.0.0'
        ], HTTPCode::OK);
    }
}
```

**Request**: `GET /status`
**Response**:
```json
{
    "status": "online",
    "timestamp": 1703123456,
    "version": "1.0.0"
}
```

### Parameter Validation

Handle user input with validation:

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
        
        // Validate user ID
        if (!is_numeric($userId) || $userId <= 0) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid user ID',
                HTTPCode::BAD_REQUEST
            );
        }

        // Simulate user data
        $user = [
            'id' => (int)$userId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'created_at' => '2024-01-01T00:00:00Z'
        ];

        return new MixedResponse($user, HTTPCode::OK);
    }
}
```

### POST Request with Data

Handle POST requests with form data:

```php
<?php
// src/Controllers/CreateUserController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\SuccessResponse;
use Donner\Utils\HTTPCode;
use Donner\Exception\DonnerException;

class CreateUserController extends AbstractController {
    public const URI = '/users';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        // Validate required fields
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

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid email format',
                HTTPCode::BAD_REQUEST
            );
        }

        // Simulate user creation
        $userId = rand(1000, 9999);
        
        return new SuccessResponse();
    }
}
```

**Request**: `POST /users`
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "age": "25"
}
```

**Response**:
```json
{
    "success": true
}
```

## Advanced Examples

### File Upload Handling

Handle file uploads with validation:

```php
<?php
// src/Controllers/UploadController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\SuccessResponse;
use Donner\Utils\HTTPCode;
use Donner\Exception\DonnerException;

class UploadController extends AbstractController {
    public const URI = '/upload';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        // Validate file upload
        $file = $this->request->getFile('photo')
            ->required('Photo is required')
            ->maxSize(5 * 1024 * 1024, 'File must be under 5MB')
            ->file();

        if (!$file || !$file->isImage()) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid image file',
                HTTPCode::BAD_REQUEST
            );
        }

        // Process file (in real app, save to storage)
        $uploadPath = 'uploads/' . uniqid() . '.' . $file->getExt();
        
        // Simulate successful upload
        return new SuccessResponse();
    }
}
```

### Paginated List Response

Create a paginated list endpoint:

```php
<?php
// src/Controllers/UsersListController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\ItemsResponse;
use Donner\Utils\HTTPCode;

class UsersListController extends AbstractController {
    public const URI = '/users';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        // Get pagination parameters
        $page = $this->request->get('page')
            ->defaultValue(1)
            ->int();
        
        $limit = $this->request->get('limit')
            ->defaultValue(10)
            ->int()
            ->positive();

        // Simulate data
        $users = [];
        for ($i = 1; $i <= $limit; $i++) {
            $users[] = [
                'id' => ($page - 1) * $limit + $i,
                'name' => "User " . (($page - 1) * $limit + $i),
                'email' => "user" . (($page - 1) * $limit + $i) . "@example.com"
            ];
        }

        $totalCount = 100; // Simulate total count
        $hasNext = $page * $limit < $totalCount;
        $hasPrevious = $page > 1;

        return (new ItemsResponse())
            ->setItems($users)
            ->setTotalCount($totalCount)
            ->setNextCursor($hasNext ? $page + 1 : null)
            ->setPreviousCursor($hasPrevious ? $page - 1 : null);
    }
}
```

**Request**: `GET /users?page=2&limit=5`
**Response**:
```json
{
    "items": [
        {
            "id": 6,
            "name": "User 6",
            "email": "user6@example.com"
        },
        {
            "id": 7,
            "name": "User 7",
            "email": "user7@example.com"
        }
    ],
    "total_count": 100,
    "previous_cursor": 1,
    "next_cursor": 3
}
```

### Custom Response Class

Create a custom response class:

```php
<?php
// src/Responses/UserResponse.php

namespace App\Responses;

use Donner\Response\AbstractResponse;
use Donner\Utils\HTTPCode;

class UserResponse extends AbstractResponse {
    public int $id;
    public string $name;
    public string $email;
    public string $status;
    public string $created_at;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $status = 'active',
        string $created_at = null
    ) {
        parent::__construct(HTTPCode::OK);
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->status = $status;
        $this->created_at = $created_at ?: date('c');
    }

    public static function create(
        int $id,
        string $name,
        string $email,
        string $status = 'active'
    ): self {
        return new self($id, $name, $email, $status);
    }
}
```

**Usage in Controller**:
```php
<?php
// src/Controllers/UserDetailController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use App\Responses\UserResponse;

class UserDetailController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        
        // Simulate user data
        return UserResponse::create(
            (int)$userId,
            'John Doe',
            'john@example.com',
            'active'
        );
    }
}
```

### Enum Validation

Use PHP enums for validation:

```php
<?php
// src/Enums/UserStatus.php

namespace App\Enums;

enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
    case PENDING = 'pending';
}
```

```php
<?php
// src/Controllers/UpdateUserStatusController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\SuccessResponse;
use Donner\Utils\HTTPCode;
use App\Enums\UserStatus;

class UpdateUserStatusController extends AbstractController {
    public const URI = '/user/{id}/status';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];
        
        $status = $this->request->get('status')
            ->required('Status is required')
            ->enum(
                array_map(fn($case) => $case->value, UserStatus::cases()),
                'Invalid status. Allowed: active, inactive, banned, pending'
            );

        // Validate user ID
        if (!is_numeric($userId) || $userId <= 0) {
            throw new \Donner\Exception\DonnerException(
                \Donner\Exception\DonnerException::INVALID_REQUEST,
                'Invalid user ID',
                HTTPCode::BAD_REQUEST
            );
        }

        // Simulate status update
        return new SuccessResponse();
    }
}
```

## Real-world Applications

### RESTful API

Complete RESTful API example:

```php
<?php
// src/Controllers/PostsController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Response\ItemsResponse;
use Donner\Response\SuccessResponse;
use Donner\Utils\HTTPCode;
use Donner\Exception\DonnerException;

class PostsController extends AbstractController {
    public const URI = '/posts';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        $page = $this->request->get('page')->defaultValue(1)->int();
        $limit = $this->request->get('limit')->defaultValue(10)->int()->positive();
        $category = $this->request->get('category')->string();

        // Simulate posts data
        $posts = $this->getPosts($page, $limit, $category);
        $totalCount = $this->getTotalPostsCount($category);

        return (new ItemsResponse())
            ->setItems($posts)
            ->setTotalCount($totalCount)
            ->setNextCursor($page * $limit < $totalCount ? $page + 1 : null)
            ->setPreviousCursor($page > 1 ? $page - 1 : null);
    }

    private function getPosts(int $page, int $limit, ?string $category): array {
        // Simulate database query
        $posts = [];
        for ($i = 1; $i <= $limit; $i++) {
            $posts[] = [
                'id' => ($page - 1) * $limit + $i,
                'title' => "Post " . (($page - 1) * $limit + $i),
                'content' => "This is the content of post " . (($page - 1) * $limit + $i),
                'category' => $category ?: 'general',
                'created_at' => date('c'),
                'author' => [
                    'id' => 1,
                    'name' => 'John Doe'
                ]
            ];
        }
        return $posts;
    }

    private function getTotalPostsCount(?string $category): int {
        return 50; // Simulate total count
    }
}
```

```php
<?php
// src/Controllers/CreatePostController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use Donner\Exception\DonnerException;

class CreatePostController extends AbstractController {
    public const URI = '/posts';
    public const ALLOWED_METHOD = self::METHOD_POST;

    public function resolve(): \Donner\Response\ResponseInterface {
        $title = $this->request->get('title')
            ->required('Title is required')
            ->string('Title must be a string');

        $content = $this->request->get('content')
            ->required('Content is required')
            ->string('Content must be a string');

        $category = $this->request->get('category')
            ->defaultValue('general')
            ->string();

        // Validate title length
        if (strlen($title) > 200) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Title must be less than 200 characters',
                HTTPCode::BAD_REQUEST
            );
        }

        // Simulate post creation
        $postId = rand(1000, 9999);
        
        return new MixedResponse([
            'id' => $postId,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'created_at' => date('c'),
            'status' => 'published'
        ], HTTPCode::CREATED);
    }
}
```

### Authentication Middleware

Simple authentication example:

```php
<?php
// src/Middleware/AuthMiddleware.php

namespace App\Middleware;

use Donner\Exception\DonnerException;
use Donner\Utils\HTTPCode;

class AuthMiddleware {
    public static function checkAuth(string $token): bool {
        // Simple token validation (in real app, use JWT or similar)
        $validTokens = ['admin123', 'user456', 'api789'];
        return in_array($token, $validTokens);
    }

    public static function requireAuth(): string {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = str_replace('Bearer ', '', $token);

        if (!self::checkAuth($token)) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid or missing authentication token',
                HTTPCode::UNAUTHORIZED
            );
        }

        return $token;
    }
}
```

```php
<?php
// src/Controllers/ProtectedController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use App\Middleware\AuthMiddleware;

class ProtectedController extends AbstractController {
    public const URI = '/protected';
    public const ALLOWED_METHOD = self::METHOD_GET;

    public function resolve(): \Donner\Response\ResponseInterface {
        // Check authentication
        $token = AuthMiddleware::requireAuth();

        return new MixedResponse([
            'message' => 'Access granted',
            'token' => $token,
            'timestamp' => time()
        ], HTTPCode::OK);
    }
}
```

## Common Patterns

### Error Handling

Centralized error handling:

```php
<?php
// src/Router.php

namespace App;

use Donner\BasicRouter;
use Donner\Exception\DonnerException;
use Donner\Utils\HTTPCode;

class Router {
    public function handleRequest(): void {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $router = BasicRouter::create()
                ->addController(new \App\Controllers\HomeController())
                ->addController(new \App\Controllers\UserController())
                ->setNotFoundController(new \App\Controllers\NotFoundController())
                ->run();
        } catch (DonnerException $exception) {
            $this->handleDonnerException($exception);
        } catch (\Exception $exception) {
            $this->handleGenericException($exception);
        }
    }

    private function handleDonnerException(DonnerException $exception): void {
        HTTPCode::set($exception->getHTTPCode());
        echo json_encode([
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'type' => 'DonnerException'
            ]
        ]);
    }

    private function handleGenericException(\Exception $exception): void {
        HTTPCode::set(HTTPCode::INTERNAL_SERVER_ERROR);
        echo json_encode([
            'error' => [
                'code' => 500,
                'message' => 'Internal server error',
                'type' => 'GenericException'
            ]
        ]);
    }
}
```

### Database Integration

Simple database integration pattern:

```php
<?php
// src/Controllers/UserController.php

namespace App\Controllers;

use Donner\Controller\AbstractController;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;
use PDO;

class UserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;

    private PDO $db;

    public function __construct() {
        parent::__construct();
        $this->db = $this->getDatabase();
    }

    public function resolve(): \Donner\Response\ResponseInterface {
        $userId = $this->params[0];

        if (!is_numeric($userId)) {
            throw new \Donner\Exception\DonnerException(
                \Donner\Exception\DonnerException::INVALID_REQUEST,
                'Invalid user ID',
                HTTPCode::BAD_REQUEST
            );
        }

        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new \Donner\Exception\DonnerException(
                \Donner\Exception\DonnerException::INVALID_REQUEST,
                'User not found',
                HTTPCode::NOT_FOUND
            );
        }

        return new MixedResponse($user, HTTPCode::OK);
    }

    private function getDatabase(): PDO {
        $dsn = 'mysql:host=localhost;dbname=myapp;charset=utf8mb4';
        return new PDO($dsn, 'username', 'password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
```

## Troubleshooting Examples

### Debug Mode

Enable debug mode for development:

```php
<?php
// src/Router.php

namespace App;

use Donner\BasicRouter;

class Router {
    private bool $debug;

    public function __construct(bool $debug = false) {
        $this->debug = $debug;
    }

    public function handleRequest(): void {
        if ($this->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        header('Content-Type: application/json; charset=UTF-8');

        try {
            $router = BasicRouter::create()
                ->addController(new \App\Controllers\HomeController())
                ->run();
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    private function handleException(\Exception $exception): void {
        if ($this->debug) {
            echo json_encode([
                'error' => [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ]
            ]);
        } else {
            echo json_encode([
                'error' => [
                    'message' => 'An error occurred'
                ]
            ]);
        }
    }
}
```

### Logging

Add logging to your application:

```php
<?php
// src/Utils/Logger.php

namespace App\Utils;

class Logger {
    private string $logFile;

    public function __construct(string $logFile = 'app.log') {
        $this->logFile = $logFile;
    }

    public function log(string $level, string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }

    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }
}
```

These examples demonstrate the flexibility and power of the Donner framework for building various types of web applications and APIs.
