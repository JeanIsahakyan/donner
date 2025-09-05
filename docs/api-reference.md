# API Reference

Complete reference documentation for all classes, methods, and interfaces in the Donner framework.

## Table of Contents

- [Router Classes](#router-classes)
- [Controller Classes](#controller-classes)
- [Request Classes](#request-classes)
- [Response Classes](#response-classes)
- [Exception Classes](#exception-classes)
- [Utility Classes](#utility-classes)

## Router Classes

### AbstractRouter

Base abstract class for all router implementations.

**Namespace**: `Donner`

**Methods**:

#### `setNotFoundController(AbstractController $controller): static`
Sets a custom 404 controller.

**Parameters**:
- `$controller` - Controller to handle 404 responses

**Returns**: `static` - Fluent interface

**Example**:
```php
$router->setNotFoundController(new CustomNotFoundController());
```

#### `addController(AbstractController $controller): static`
Adds a controller to the router.

**Parameters**:
- `$controller` - Controller instance to add

**Returns**: `static` - Fluent interface

**Example**:
```php
$router->addController(new HomeController());
```

#### `create(): static`
Static factory method to create router instance.

**Returns**: `static` - New router instance

**Example**:
```php
$router = BasicRouter::create();
```

### BasicRouter

Concrete implementation of the router.

**Namespace**: `Donner`

**Extends**: `AbstractRouter`

**Methods**:

#### `run(): void`
Processes the current request and outputs the response.

**Example**:
```php
$router->run();
```

### RouterInterface

Interface that all routers must implement.

**Namespace**: `Donner`

**Methods**:

#### `run(): void`
Processes the current request.

## Controller Classes

### AbstractController

Base abstract class for all controllers.

**Namespace**: `Donner\Controller`

**Implements**: `ControllerInterface`

**Properties**:
- `protected RequestParams $request` - Request parameters instance
- `protected array $params` - Route parameters

**Methods**:

#### `setParams(array $params): static`
Sets route parameters extracted from URI.

**Parameters**:
- `$params` - Array of parameter values

**Returns**: `static` - Fluent interface

#### `resolve(): ResponseInterface`
Abstract method that must be implemented by concrete controllers.

**Returns**: `ResponseInterface` - Response to send to client

**Throws**: `DonnerException` - If request cannot be resolved

### ControllerInterface

Interface that all controllers must implement.

**Namespace**: `Donner\Controller`

**Constants**:
- `URI` - Route pattern (e.g., '/user/{id}')
- `METHOD_ALL` - Accept all HTTP methods
- `METHOD_GET` - GET method only
- `METHOD_POST` - POST method only
- `ALLOWED_METHOD` - Allowed HTTP method for this controller

**Methods**:

#### `resolve(): ResponseInterface`
Resolves the request and returns a response.

### NotFoundController

Default 404 controller.

**Namespace**: `Donner\Controller`

**Extends**: `AbstractController`

**Methods**:

#### `resolve(): ResponseInterface`
Returns a 404 Not Found response.

## Request Classes

### RequestParams

Manages request parameters and files.

**Namespace**: `Donner\Request`

**Methods**:

#### `create(array $params, array $files): self`
Static factory method to create instance.

**Parameters**:
- `$params` - GET/POST parameters
- `$files` - Uploaded files

**Returns**: `self` - New instance

#### `exists(string $param): bool`
Checks if a parameter exists.

**Parameters**:
- `$param` - Parameter name

**Returns**: `bool` - True if exists

#### `fileExists(string $param): bool`
Checks if a file parameter exists.

**Parameters**:
- `$param` - File parameter name

**Returns**: `bool` - True if exists

#### `get(string $param): RequestParam`
Gets a parameter for validation.

**Parameters**:
- `$param` - Parameter name

**Returns**: `RequestParam` - Parameter instance

#### `getFile(string $param): RequestParamUploadFile`
Gets a file parameter for validation.

**Parameters**:
- `$param` - File parameter name

**Returns**: `RequestParamUploadFile` - File parameter instance

#### `getAll(): array`
Gets all parameters and files.

**Returns**: `array` - Combined parameters and files

### RequestParam

Handles individual parameter validation.

**Namespace**: `Donner\Request`

**Methods**:

#### `__construct(string $param, ?string $value)`
Constructor.

**Parameters**:
- `$param` - Parameter name
- `$value` - Parameter value

#### `defaultValue(mixed $value): self`
Sets default value for parameter.

**Parameters**:
- `$value` - Default value

**Returns**: `self` - Fluent interface

#### `required(string $message, int $error_code): self`
Marks parameter as required.

**Parameters**:
- `$message` - Error message template
- `$error_code` - Error code

**Returns**: `self` - Fluent interface

**Throws**: `DonnerException` - If parameter is missing

#### `int(string $message, int $error_code): int`
Validates and returns integer value.

**Parameters**:
- `$message` - Error message template
- `$error_code` - Error code

**Returns**: `int` - Integer value

**Throws**: `DonnerException` - If validation fails

#### `string(string $message, int $error_code): string`
Validates and returns string value.

**Parameters**:
- `$message` - Error message template
- `$error_code` - Error code

**Returns**: `string` - String value

**Throws**: `DonnerException` - If validation fails

#### `bool(): bool`
Converts value to boolean.

**Returns**: `bool` - Boolean value

#### `positive(string $error_message, int $error_code): int`
Validates positive integer.

**Parameters**:
- `$error_message` - Error message template
- `$error_code` - Error code

**Returns**: `int` - Positive integer

**Throws**: `DonnerException` - If not positive

#### `positiveList(string $error_message, int $error_code): array`
Validates comma-separated list of positive integers.

**Parameters**:
- `$error_message` - Error message template
- `$error_code` - Error code

**Returns**: `array` - Array of positive integers

**Throws**: `DonnerException` - If validation fails

#### `enum(array $enum, string $error_message, int $error_code): string`
Validates against enum values.

**Parameters**:
- `$enum` - Allowed values array
- `$error_message` - Error message template
- `$error_code` - Error code

**Returns**: `string` - Validated value

**Throws**: `DonnerException` - If not in enum

### RequestParamUploadFile

Handles file upload validation.

**Namespace**: `Donner\Request`

**Methods**:

#### `__construct(string $param, ?array $value)`
Constructor.

**Parameters**:
- `$param` - File parameter name
- `$value` - File data array

#### `required(string $message, int $error_code): self`
Marks file as required.

**Parameters**:
- `$message` - Error message template
- `$error_code` - Error code

**Returns**: `self` - Fluent interface

**Throws**: `DonnerException` - If file is missing

#### `maxSize(int $max_size, string $message, int $error_code): self`
Validates file size.

**Parameters**:
- `$max_size` - Maximum size in bytes
- `$message` - Error message template
- `$error_code` - Error code

**Returns**: `self` - Fluent interface

**Throws**: `DonnerException` - If file too large

#### `file(): ?UploadFile`
Returns UploadFile instance.

**Returns**: `?UploadFile` - File instance or null

### UploadFile

Manages uploaded files.

**Namespace**: `Donner\Request`

**Methods**:

#### `create(array $input): ?self`
Static factory method.

**Parameters**:
- `$input` - File data array

**Returns**: `?self` - File instance or null

#### `getPath(): ?string`
Gets file path.

**Returns**: `?string` - File path

#### `setPath(?string $path): self`
Sets file path.

**Parameters**:
- `$path` - File path

**Returns**: `self` - Fluent interface

#### `getMime(): ?string`
Gets MIME type.

**Returns**: `?string` - MIME type

#### `setMime(?string $mime): self`
Sets MIME type.

**Parameters**:
- `$mime` - MIME type

**Returns**: `self` - Fluent interface

#### `getName(): ?string`
Gets file name.

**Returns**: `?string` - File name

#### `setName(?string $name): self`
Sets file name.

**Parameters**:
- `$name` - File name

**Returns**: `self` - Fluent interface

#### `getSize(): ?int`
Gets file size.

**Returns**: `?int` - File size in bytes

#### `setSize(?int $size): self`
Sets file size.

**Parameters**:
- `$size` - File size

**Returns**: `self` - Fluent interface

#### `getExt(): ?string`
Gets file extension.

**Returns**: `?string` - File extension

#### `setExt(?string $ext): self`
Sets file extension.

**Parameters**:
- `$ext` - File extension

**Returns**: `self` - Fluent interface

#### `isImage(): bool`
Checks if file is an image.

**Returns**: `bool` - True if image

#### `clear(): void`
Cleans up temporary file.

## Response Classes

### ResponseInterface

Interface for all response types.

**Namespace**: `Donner\Response`

**Methods**:

#### `getResponse(): mixed`
Gets response data.

**Returns**: `mixed` - Response data

#### `getHTTPCode(): HTTPCode`
Gets HTTP status code.

**Returns**: `HTTPCode` - Status code enum

### AbstractResponse

Base response class with reflection-based serialization.

**Namespace**: `Donner\Response`

**Extends**: `AbstractResponseInternal`

**Methods**:

#### `__construct(HTTPCode $http_code)`
Constructor.

**Parameters**:
- `$http_code` - HTTP status code

#### `create(HTTPCode $http_code): static`
Static factory method.

**Parameters**:
- `$http_code` - HTTP status code

**Returns**: `static` - New instance

#### `getResponse(): array`
Gets serialized response data.

**Returns**: `array` - Serialized data

### MixedResponse

Generic response for any data type.

**Namespace**: `Donner\Response`

**Extends**: `AbstractResponseInternal`

**Methods**:

#### `__construct(mixed $response, HTTPCode $http_code)`
Constructor.

**Parameters**:
- `$response` - Response data
- `$http_code` - HTTP status code

#### `getResponse(): mixed`
Gets response data.

**Returns**: `mixed` - Response data

### SuccessResponse

Simple success response.

**Namespace**: `Donner\Response`

**Extends**: `AbstractResponse`

**Properties**:
- `public bool $success` - Success flag

**Methods**:

#### `isSuccess(): bool`
Checks if successful.

**Returns**: `bool` - True if successful

#### `setSuccess(bool $success): SuccessResponse`
Sets success flag.

**Parameters**:
- `$success` - Success flag

**Returns**: `SuccessResponse` - Fluent interface

### ItemsResponse

Paginated list response.

**Namespace**: `Donner\Response`

**Extends**: `AbstractResponse`

**Properties**:
- `public array $items` - Items array
- `public ?int $total_count` - Total count
- `public $previous_cursor` - Previous cursor
- `public $next_cursor` - Next cursor

**Methods**:

#### `getTotalCount(): int`
Gets total count.

**Returns**: `int` - Total count

#### `setTotalCount(int $total_count): self`
Sets total count.

**Parameters**:
- `$total_count` - Total count

**Returns**: `self` - Fluent interface

#### `getItems(): array`
Gets items array.

**Returns**: `array` - Items array

#### `setItems(array $items): self`
Sets items array.

**Parameters**:
- `$items` - Items array

**Returns**: `self` - Fluent interface

#### `getPreviousCursor()`
Gets previous cursor.

**Returns**: `mixed` - Previous cursor

#### `setPreviousCursor($previous_cursor)`
Sets previous cursor.

**Parameters**:
- `$previous_cursor` - Previous cursor

**Returns**: `self` - Fluent interface

#### `getNextCursor()`
Gets next cursor.

**Returns**: `mixed` - Next cursor

#### `setNextCursor($next_cursor)`
Sets next cursor.

**Parameters**:
- `$next_cursor` - Next cursor

**Returns**: `self` - Fluent interface

### RedirectResponse

HTTP redirect response.

**Namespace**: `Donner\Response`

**Extends**: `AbstractResponseInternal`

**Methods**:

#### `__construct(string $redirect_uri, array $params, HTTPCode $http_code)`
Constructor.

**Parameters**:
- `$redirect_uri` - Redirect URI
- `$params` - Query parameters
- `$http_code` - HTTP status code

**Throws**: `DonnerException` - If URI is invalid

#### `getResponse(): string|array`
Gets redirect response.

**Returns**: `string|array` - Redirect response

**Throws**: `DonnerException` - If URI is invalid

## Exception Classes

### DonnerException

Custom exception with HTTP code support.

**Namespace**: `Donner\Exception`

**Extends**: `Exception`

**Constants**:
- `INVALID_REQUEST` - Invalid request error code

**Properties**:
- `private HTTPCode $http_code` - HTTP status code

**Methods**:

#### `__construct(int $code, string $message, HTTPCode $http_code)`
Constructor.

**Parameters**:
- `$code` - Exception code
- `$message` - Exception message
- `$http_code` - HTTP status code

#### `getHTTPCode(): HTTPCode`
Gets HTTP status code.

**Returns**: `HTTPCode` - HTTP status code

## Utility Classes

### HTTPCode

PHP 8.1 enum for HTTP status codes.

**Namespace**: `Donner\Utils`

**Enum Cases**:
- `CONTINUE = 100`
- `OK = 200`
- `CREATED = 201`
- `ACCEPTED = 202`
- `NON_AUTHORITATIVE = 203`
- `NO_CONTENT = 204`
- `RESET_CONTENT = 205`
- `PARTIAL_CONTENT = 206`
- `MULTIPLE_CHOICES = 300`
- `MOVED_PERMANENTLY = 301`
- `FOUND = 302`
- `SEE_OTHER = 303`
- `NOT_MODIFIED = 304`
- `USE_PROXY = 305`
- `TEMPORARY_REDIRECT = 307`
- `BAD_REQUEST = 400`
- `UNAUTHORIZED = 401`
- `PAYMENT_REQUIRED = 402`
- `FORBIDDEN = 403`
- `NOT_FOUND = 404`
- `METHOD_NOT_ALLOWED = 405`
- `NOT_ACCEPTABLE = 406`
- `CONFLICT = 409`
- `GONE = 410`
- `LENGTH_REQUIRED = 411`
- `PRECONDITION_FAILED = 412`
- `REQUEST_ENTITY_TOO_LARGE = 413`
- `REQUEST_URI_TOO_LONG = 414`
- `UNSUPPORTED_MEDIA_TYPE = 415`
- `REQUEST_RANGE_NOT_SATISFIABLE = 416`
- `EXPECTATION_FAILED = 417`
- `INTERNAL_SERVER_ERROR = 500`
- `NOT_IMPLEMENTED = 501`
- `SERVICE_UNAVAILABLE = 503`

**Methods**:

#### `set(HTTPCode $code): void`
Sets HTTP response code.

**Parameters**:
- `$code` - HTTP status code

**Example**:
```php
HTTPCode::set(HTTPCode::OK);
```
