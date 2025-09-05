# Architecture Overview

This document provides a comprehensive overview of the Donner framework's architecture, design patterns, and internal structure.

## Table of Contents

- [High-Level Architecture](#high-level-architecture)
- [Core Components](#core-components)
- [Design Patterns](#design-patterns)
- [Request Flow](#request-flow)
- [Response Flow](#response-flow)
- [Extension Points](#extension-points)
- [Performance Considerations](#performance-considerations)

## High-Level Architecture

Donner follows a layered architecture pattern with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────┐
│                    Client Request                           │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                 Web Server                                  │
│              (Apache/Nginx/PHP)                            │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                   index.php                                 │
│              (Entry Point)                                 │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                   Router                                    │
│              (BasicRouter)                                 │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                 Controller                                  │
│            (AbstractController)                            │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                  Response                                   │
│            (ResponseInterface)                             │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                 Client Response                             │
└─────────────────────────────────────────────────────────────┘
```

## Core Components

### 1. Router System

The router system is responsible for matching incoming requests to appropriate controllers.

#### AbstractRouter
- **Purpose**: Base class providing common routing functionality
- **Responsibilities**:
  - Controller registration and management
  - URI pattern matching with regex
  - Parameter extraction from URI patterns
  - 404 handling
  - OPTIONS request handling

#### BasicRouter
- **Purpose**: Concrete implementation of the router
- **Responsibilities**:
  - Request processing
  - Exception handling
  - Response rendering

#### Key Features:
- **Pattern Matching**: Uses regex to match URI patterns like `/user/{id}`
- **Parameter Extraction**: Automatically extracts parameters from matched patterns
- **Method Filtering**: Supports GET, POST, and ALL methods
- **Fallback Handling**: Provides 404 controller for unmatched routes

### 2. Controller System

Controllers handle business logic and return responses.

#### AbstractController
- **Purpose**: Base class for all controllers
- **Responsibilities**:
  - Request parameter initialization
  - Route parameter management
  - Request validation setup

#### ControllerInterface
- **Purpose**: Contract that all controllers must implement
- **Defines**:
  - URI pattern constant
  - HTTP method constant
  - resolve() method signature

#### Key Features:
- **Method Binding**: Each controller specifies allowed HTTP methods
- **Parameter Access**: Route parameters available via `$this->params`
- **Request Access**: Request data available via `$this->request`

### 3. Request System

The request system handles input validation and sanitization.

#### RequestParams
- **Purpose**: Central request data manager
- **Responsibilities**:
  - Parameter and file management
  - Factory methods for validation objects

#### RequestParam
- **Purpose**: Individual parameter validation
- **Features**:
  - Fluent validation API
  - Type conversion (int, string, bool)
  - Enum validation
  - Required field validation
  - Default value support

#### RequestParamUploadFile
- **Purpose**: File upload validation
- **Features**:
  - File size validation
  - Required file validation
  - UploadFile object creation

#### UploadFile
- **Purpose**: File management and processing
- **Features**:
  - MIME type detection
  - File extension extraction
  - Image type checking
  - Automatic cleanup

### 4. Response System

The response system provides structured output formatting.

#### ResponseInterface
- **Purpose**: Contract for all response types
- **Defines**:
  - getResponse() method
  - getHTTPCode() method

#### AbstractResponse
- **Purpose**: Base response with reflection-based serialization
- **Features**:
  - Automatic property serialization
  - Nested response support
  - Type-safe HTTP code handling

#### Response Types:
- **MixedResponse**: Generic data response
- **SuccessResponse**: Simple success indicator
- **ItemsResponse**: Paginated list response
- **RedirectResponse**: HTTP redirect response

### 5. Exception System

#### DonnerException
- **Purpose**: Custom exception with HTTP code support
- **Features**:
  - HTTP status code integration
  - Custom error codes
  - Structured error handling

### 6. Utility System

#### HTTPCode
- **Purpose**: PHP 8.1 enum for HTTP status codes
- **Features**:
  - Type-safe status codes
  - Static set() method for response codes
  - Complete HTTP status code coverage

## Design Patterns

### 1. Factory Pattern

Used for object creation throughout the framework:

```php
// Router creation
$router = BasicRouter::create();

// Response creation
$response = MixedResponse::create(HTTPCode::OK);

// Request parameter creation
$param = RequestParam::create('name', 'value');
```

### 2. Fluent Interface Pattern

Used for method chaining in validation:

```php
$name = $this->request->get('name')
    ->required('Name is required')
    ->string('Name must be a string');
```

### 3. Strategy Pattern

Different response types for different use cases:

```php
// Different response strategies
new MixedResponse($data);
new SuccessResponse();
new ItemsResponse();
new RedirectResponse($url);
```

### 4. Template Method Pattern

AbstractController defines the template, concrete controllers implement specific behavior:

```php
abstract class AbstractController {
    // Template method
    public function __construct() {
        $this->initRequest(); // Common initialization
    }
    
    // Abstract method for subclasses
    abstract public function resolve(): ResponseInterface;
}
```

### 5. Reflection Pattern

Used in AbstractResponse for automatic serialization:

```php
public function getResponse(): array {
    foreach ($this->_reflection->getProperties() as $property) {
        // Serialize public properties
    }
}
```

## Request Flow

### 1. Request Reception
```
Client Request → Web Server → index.php
```

### 2. Router Initialization
```
index.php → Router::handleRequest() → BasicRouter::create()
```

### 3. Route Matching
```
BasicRouter::run() → AbstractRouter::tryRun() → Pattern Matching
```

### 4. Controller Resolution
```
Matched Route → Controller::resolve() → Response Creation
```

### 5. Response Rendering
```
Response → AbstractRouter::renderResponse() → Client
```

### Detailed Flow:

1. **Request arrives** at web server
2. **Web server** routes to `index.php`
3. **Router initialization** creates BasicRouter instance
4. **Route matching** uses regex to find matching controller
5. **Parameter extraction** from URI pattern
6. **Controller instantiation** with parameters
7. **Request validation** using RequestParam objects
8. **Business logic** execution in controller
9. **Response creation** using response classes
10. **Response serialization** using reflection
11. **HTTP headers** and status code setting
12. **Response output** to client

## Response Flow

### 1. Response Creation
```
Controller → Response Object → ResponseInterface
```

### 2. Response Serialization
```
ResponseInterface → getResponse() → Array/Data
```

### 3. HTTP Code Setting
```
ResponseInterface → getHTTPCode() → HTTPCode::set()
```

### 4. Output Rendering
```
Serialized Data → JSON/HTML → Client
```

## Extension Points

### 1. Custom Controllers

Extend AbstractController to create custom controllers:

```php
class CustomController extends AbstractController {
    public const URI = '/custom/{id}';
    public const ALLOWED_METHOD = self::METHOD_GET;
    
    public function resolve(): ResponseInterface {
        // Custom logic
        return new CustomResponse();
    }
}
```

### 2. Custom Responses

Extend AbstractResponse for custom response types:

```php
class CustomResponse extends AbstractResponse {
    public string $message;
    public array $data;
    
    public function __construct(string $message, array $data) {
        parent::__construct(HTTPCode::OK);
        $this->message = $message;
        $this->data = $data;
    }
}
```

### 3. Custom Routers

Implement RouterInterface for custom routing logic:

```php
class CustomRouter implements RouterInterface {
    public function run(): void {
        // Custom routing logic
    }
}
```

### 4. Middleware Integration

Add middleware support by extending the router:

```php
class MiddlewareRouter extends BasicRouter {
    private array $middleware = [];
    
    public function addMiddleware(callable $middleware): self {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    public function run(): void {
        foreach ($this->middleware as $middleware) {
            $middleware();
        }
        parent::run();
    }
}
```

## Performance Considerations

### 1. Memory Management

- **UploadFile**: Automatic cleanup in destructor
- **Reflection**: Cached reflection objects in AbstractResponse
- **Parameters**: Minimal memory footprint for request data

### 2. Request Processing

- **Pattern Matching**: Efficient regex compilation
- **Parameter Extraction**: Single-pass extraction
- **Validation**: Lazy validation (only when accessed)

### 3. Response Generation

- **Serialization**: Reflection-based (one-time cost)
- **JSON Encoding**: Native PHP json_encode()
- **Headers**: Minimal header overhead

### 4. Caching Opportunities

- **Route Patterns**: Pre-compiled regex patterns
- **Reflection Objects**: Cached reflection instances
- **Response Objects**: Immutable response objects

### 5. Scalability Considerations

- **Stateless Design**: No session state management
- **Lightweight**: Minimal framework overhead
- **Modular**: Easy to extend and modify
- **Memory Efficient**: Automatic cleanup and minimal allocations

## Security Considerations

### 1. Input Validation

- **Type Safety**: Strong typing with validation
- **Sanitization**: Built-in parameter sanitization
- **File Uploads**: Secure file handling with validation

### 2. Error Handling

- **Information Disclosure**: Controlled error messages
- **Exception Safety**: Proper exception handling
- **HTTP Codes**: Appropriate status code usage

### 3. Request Processing

- **Method Validation**: HTTP method checking
- **Parameter Extraction**: Safe parameter handling
- **File Security**: Upload file validation

This architecture provides a solid foundation for building web applications while maintaining simplicity, performance, and extensibility.
