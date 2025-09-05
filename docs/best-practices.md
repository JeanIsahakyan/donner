# Best Practices

This guide outlines recommended practices and patterns for building applications with the Donner framework.

## Table of Contents

- [Code Organization](#code-organization)
- [Controller Design](#controller-design)
- [Request Validation](#request-validation)
- [Response Design](#response-design)
- [Error Handling](#error-handling)
- [Security Practices](#security-practices)
- [Performance Optimization](#performance-optimization)
- [Testing Strategies](#testing-strategies)
- [Deployment Guidelines](#deployment-guidelines)

## Code Organization

### Project Structure

Organize your Donner application with a clear, scalable structure:

```
my-app/
├── composer.json
├── index.php
├── .htaccess
├── src/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── UserController.php
│   │   ├── AuthController.php
│   │   └── Api/
│   │       ├── V1/
│   │       │   ├── UserController.php
│   │       │   └── PostController.php
│   │       └── V2/
│   │           └── UserController.php
│   ├── Responses/
│   │   ├── UserResponse.php
│   │   ├── PostResponse.php
│   │   └── ErrorResponse.php
│   ├── Services/
│   │   ├── UserService.php
│   │   ├── AuthService.php
│   │   └── EmailService.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Post.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   └── CorsMiddleware.php
│   ├── Utils/
│   │   ├── Logger.php
│   │   └── Validator.php
│   └── Router.php
├── config/
│   ├── database.php
│   └── app.php
├── public/
│   ├── css/
│   ├── js/
│   └── images/
└── tests/
    ├── Controllers/
    └── Services/
```

### Namespace Conventions

Use consistent namespace patterns:

```php
// Controllers
namespace App\Controllers;
namespace App\Controllers\Api\V1;

// Services
namespace App\Services;

// Responses
namespace App\Responses;

// Models
namespace App\Models;

// Middleware
namespace App\Middleware;
```

### File Naming

Follow PSR-4 naming conventions:

- **Controllers**: `UserController.php`, `CreateUserController.php`
- **Services**: `UserService.php`, `EmailService.php`
- **Responses**: `UserResponse.php`, `ErrorResponse.php`
- **Models**: `User.php`, `Post.php`

## Controller Design

### Single Responsibility

Each controller should handle one specific resource or action:

```php
// ✅ Good: Single responsibility
class UserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;
    
    public function resolve(): ResponseInterface {
        // Handle user retrieval only
    }
}

// ❌ Bad: Multiple responsibilities
class UserController extends AbstractController {
    public const URI = '/user';
    public const ALLOWED_METHOD = self::METHOD_ALL;
    
    public function resolve(): ResponseInterface {
        // Handles GET, POST, PUT, DELETE - too much!
    }
}
```

### Controller Naming

Use descriptive names that indicate the action:

```php
// ✅ Good: Clear action indication
class CreateUserController extends AbstractController {
    public const URI = '/users';
    public const ALLOWED_METHOD = self::METHOD_POST;
}

class UpdateUserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_PUT;
}

class DeleteUserController extends AbstractController {
    public const URI = '/user/{id}';
    public const ALLOWED_METHOD = self::METHOD_DELETE;
}
```

### Controller Organization

Group related controllers in subdirectories:

```php
// API versioning
namespace App\Controllers\Api\V1;
namespace App\Controllers\Api\V2;

// Feature grouping
namespace App\Controllers\Admin;
namespace App\Controllers\Public;
```

### Dependency Injection

Use constructor injection for dependencies:

```php
class UserController extends AbstractController {
    private UserService $userService;
    private Logger $logger;
    
    public function __construct(UserService $userService, Logger $logger) {
        parent::__construct();
        $this->userService = $userService;
        $this->logger = $logger;
    }
    
    public function resolve(): ResponseInterface {
        $userId = $this->params[0];
        $user = $this->userService->findById($userId);
        
        $this->logger->info('User retrieved', ['user_id' => $userId]);
        
        return new UserResponse($user);
    }
}
```

## Request Validation

### Validation Strategy

Validate early and fail fast:

```php
public function resolve(): ResponseInterface {
    // Validate route parameters first
    $userId = $this->validateUserId($this->params[0]);
    
    // Then validate request parameters
    $name = $this->request->get('name')
        ->required('Name is required')
        ->string('Name must be a string');
    
    $email = $this->request->get('email')
        ->required('Email is required')
        ->string('Email must be a string');
    
    // Validate business rules
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new DonnerException(
            DonnerException::INVALID_REQUEST,
            'Invalid email format',
            HTTPCode::BAD_REQUEST
        );
    }
    
    // Process request
}
```

### Custom Validation

Create reusable validation methods:

```php
class UserController extends AbstractController {
    private function validateUserId(string $userId): int {
        if (!is_numeric($userId) || $userId <= 0) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid user ID',
                HTTPCode::BAD_REQUEST
            );
        }
        return (int)$userId;
    }
    
    private function validateEmail(string $email): string {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid email format',
                HTTPCode::BAD_REQUEST
            );
        }
        return $email;
    }
}
```

### Enum Validation

Use enums for controlled values:

```php
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}

// In controller
$status = $this->request->get('status')
    ->required('Status is required')
    ->enum(
        array_map(fn($case) => $case->value, UserStatus::cases()),
        'Invalid status'
    );
```

## Response Design

### Consistent Response Format

Use consistent response structures:

```php
// Success response
{
    "success": true,
    "data": {
        "id": 123,
        "name": "John Doe"
    },
    "meta": {
        "timestamp": "2024-01-01T00:00:00Z"
    }
}

// Error response
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Invalid input data",
        "details": {
            "email": "Invalid email format"
        }
    }
}
```

### Custom Response Classes

Create specific response classes for different data types:

```php
class UserResponse extends AbstractResponse {
    public int $id;
    public string $name;
    public string $email;
    public string $status;
    public string $created_at;
    
    public function __construct(User $user) {
        parent::__construct(HTTPCode::OK);
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->email = $user->getEmail();
        $this->status = $user->getStatus();
        $this->created_at = $user->getCreatedAt()->format('c');
    }
}
```

### Pagination

Use ItemsResponse for paginated data:

```php
public function resolve(): ResponseInterface {
    $page = $this->request->get('page')->defaultValue(1)->int();
    $limit = $this->request->get('limit')->defaultValue(10)->int()->positive();
    
    $users = $this->userService->getUsers($page, $limit);
    $totalCount = $this->userService->getTotalCount();
    
    return (new ItemsResponse())
        ->setItems($users)
        ->setTotalCount($totalCount)
        ->setNextCursor($page * $limit < $totalCount ? $page + 1 : null)
        ->setPreviousCursor($page > 1 ? $page - 1 : null);
}
```

## Error Handling

### Exception Hierarchy

Create custom exception classes:

```php
class ValidationException extends DonnerException {
    public const VALIDATION_ERROR = 1000;
    
    public function __construct(string $message, array $details = []) {
        parent::__construct(
            self::VALIDATION_ERROR,
            $message,
            HTTPCode::BAD_REQUEST
        );
        $this->details = $details;
    }
}

class NotFoundException extends DonnerException {
    public const NOT_FOUND = 1001;
    
    public function __construct(string $resource, string $identifier) {
        parent::__construct(
            self::NOT_FOUND,
            "{$resource} with identifier '{$identifier}' not found",
            HTTPCode::NOT_FOUND
        );
    }
}
```

### Centralized Error Handling

Handle errors consistently in your router:

```php
class Router {
    public function handleRequest(): void {
        try {
            $router = BasicRouter::create()
                ->addController(new HomeController())
                ->run();
        } catch (ValidationException $e) {
            $this->handleValidationError($e);
        } catch (NotFoundException $e) {
            $this->handleNotFoundError($e);
        } catch (DonnerException $e) {
            $this->handleDonnerError($e);
        } catch (\Exception $e) {
            $this->handleGenericError($e);
        }
    }
    
    private function handleValidationError(ValidationException $e): void {
        HTTPCode::set(HTTPCode::BAD_REQUEST);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => $e->getDetails()
            ]
        ]);
    }
}
```

### Logging

Implement comprehensive logging:

```php
class UserController extends AbstractController {
    private Logger $logger;
    
    public function resolve(): ResponseInterface {
        try {
            $userId = $this->params[0];
            $this->logger->info('User request started', ['user_id' => $userId]);
            
            $user = $this->userService->findById($userId);
            
            $this->logger->info('User retrieved successfully', [
                'user_id' => $userId,
                'user_name' => $user->getName()
            ]);
            
            return new UserResponse($user);
        } catch (NotFoundException $e) {
            $this->logger->warning('User not found', [
                'user_id' => $this->params[0],
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('User request failed', [
                'user_id' => $this->params[0],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
```

## Security Practices

### Input Sanitization

Always sanitize and validate input:

```php
public function resolve(): ResponseInterface {
    $name = $this->request->get('name')
        ->required('Name is required')
        ->string('Name must be a string');
    
    // Sanitize HTML
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    
    // Validate length
    if (strlen($name) > 100) {
        throw new ValidationException('Name too long');
    }
    
    return new SuccessResponse();
}
```

### File Upload Security

Validate file uploads thoroughly:

```php
public function resolve(): ResponseInterface {
    $file = $this->request->getFile('upload')
        ->required('File is required')
        ->maxSize(5 * 1024 * 1024, 'File too large')
        ->file();
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file->getMime(), $allowedTypes)) {
        throw new ValidationException('Invalid file type');
    }
    
    // Validate file extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file->getExt(), $allowedExtensions)) {
        throw new ValidationException('Invalid file extension');
    }
    
    // Process file
    return new SuccessResponse();
}
```

### Authentication

Implement proper authentication:

```php
class AuthMiddleware {
    public static function requireAuth(): User {
        $token = self::extractToken();
        
        if (!$token) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Authentication required',
                HTTPCode::UNAUTHORIZED
            );
        }
        
        $user = self::validateToken($token);
        if (!$user) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Invalid token',
                HTTPCode::UNAUTHORIZED
            );
        }
        
        return $user;
    }
    
    private static function extractToken(): ?string {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
```

### Rate Limiting

Implement rate limiting:

```php
class RateLimiter {
    private array $requests = [];
    
    public function checkLimit(string $ip, int $maxRequests = 100, int $window = 3600): void {
        $now = time();
        $windowStart = $now - $window;
        
        // Clean old requests
        $this->requests[$ip] = array_filter(
            $this->requests[$ip] ?? [],
            fn($timestamp) => $timestamp > $windowStart
        );
        
        // Check limit
        if (count($this->requests[$ip]) >= $maxRequests) {
            throw new DonnerException(
                DonnerException::INVALID_REQUEST,
                'Rate limit exceeded',
                HTTPCode::TOO_MANY_REQUESTS
            );
        }
        
        // Record request
        $this->requests[$ip][] = $now;
    }
}
```

## Performance Optimization

### Caching

Implement caching where appropriate:

```php
class UserController extends AbstractController {
    private CacheInterface $cache;
    
    public function resolve(): ResponseInterface {
        $userId = $this->params[0];
        $cacheKey = "user_{$userId}";
        
        // Try cache first
        $user = $this->cache->get($cacheKey);
        if (!$user) {
            $user = $this->userService->findById($userId);
            $this->cache->set($cacheKey, $user, 3600); // 1 hour
        }
        
        return new UserResponse($user);
    }
}
```

### Database Optimization

Use efficient database queries:

```php
class UserService {
    public function getUsers(int $page, int $limit): array {
        $offset = ($page - 1) * $limit;
        
        // Use prepared statements
        $stmt = $this->db->prepare(
            'SELECT id, name, email FROM users 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### Memory Management

Be mindful of memory usage:

```php
class LargeDataController extends AbstractController {
    public function resolve(): ResponseInterface {
        // Process data in chunks
        $chunks = $this->processDataInChunks();
        
        return new MixedResponse([
            'chunks' => $chunks,
            'total' => count($chunks)
        ]);
    }
    
    private function processDataInChunks(): array {
        $chunks = [];
        $chunkSize = 1000;
        
        for ($i = 0; $i < $this->getTotalCount(); $i += $chunkSize) {
            $chunk = $this->getDataChunk($i, $chunkSize);
            $chunks[] = $chunk;
            
            // Free memory
            unset($chunk);
        }
        
        return $chunks;
    }
}
```

## Testing Strategies

### Unit Testing

Test individual components:

```php
class UserControllerTest extends PHPUnit\Framework\TestCase {
    public function testResolveReturnsUserResponse(): void {
        $controller = new UserController();
        $controller->setParams(['123']);
        
        $response = $controller->resolve();
        
        $this->assertInstanceOf(UserResponse::class, $response);
        $this->assertEquals(HTTPCode::OK, $response->getHTTPCode());
    }
}
```

### Integration Testing

Test complete request flows:

```php
class UserApiTest extends PHPUnit\Framework\TestCase {
    public function testGetUserReturnsValidResponse(): void {
        $router = BasicRouter::create()
            ->addController(new UserController());
        
        // Mock request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/user/123';
        
        ob_start();
        $router->run();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(200, http_response_code());
        $this->assertArrayHasKey('id', $response);
    }
}
```

## Deployment Guidelines

### Environment Configuration

Use environment-specific configuration:

```php
class Config {
    private array $config;
    
    public function __construct() {
        $env = $_ENV['APP_ENV'] ?? 'production';
        $this->config = require "config/{$env}.php";
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return $this->config[$key] ?? $default;
    }
}
```

### Production Settings

Configure for production:

```php
// config/production.php
return [
    'debug' => false,
    'log_level' => 'error',
    'cache' => true,
    'database' => [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS']
    ]
];
```

### Monitoring

Implement monitoring and health checks:

```php
class HealthController extends AbstractController {
    public const URI = '/health';
    public const ALLOWED_METHOD = self::METHOD_GET;
    
    public function resolve(): ResponseInterface {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage()
        ];
        
        $status = in_array(false, $checks) ? 'unhealthy' : 'healthy';
        
        return new MixedResponse([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => time()
        ]);
    }
}
```

Following these best practices will help you build robust, maintainable, and performant applications with the Donner framework.
