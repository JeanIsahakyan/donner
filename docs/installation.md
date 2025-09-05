# Installation Guide

This guide covers different ways to install and set up the Donner framework.

## System Requirements

### PHP Version
- **Minimum**: PHP 8.1
- **Recommended**: PHP 8.2 or higher
- **Extensions**: `ext-json` (required)

### Server Requirements
- Apache 2.4+ with mod_rewrite enabled
- Nginx 1.18+ with PHP-FPM
- PHP built-in server (for development)

### Development Tools
- Composer 2.0+
- Git (for version control)
- IDE with PHP 8.1+ support

## Installation Methods

### Method 1: Composer (Recommended)

#### For New Projects

```bash
# Create new project
composer create-project ji/donner my-app

# Or create manually
mkdir my-donner-app
cd my-donner-app
composer init
composer require ji/donner
```

#### For Existing Projects

```bash
# Add to existing project
composer require ji/donner

# Or add to composer.json
{
    "require": {
        "ji/donner": "^1.0"
    }
}
```

### Method 2: Git Clone

```bash
# Clone the repository
git clone https://github.com/your-repo/donner.git
cd donner

# Install dependencies
composer install
```

### Method 3: Download ZIP

1. Download the latest release from GitHub
2. Extract to your project directory
3. Run `composer install` in the extracted folder

## Project Structure Setup

After installation, organize your project as follows:

```
my-donner-app/
├── composer.json
├── composer.lock
├── vendor/
│   └── autoload.php
├── index.php
├── .htaccess (for Apache)
├── src/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── UserController.php
│   │   └── NotFoundController.php
│   ├── Responses/
│   │   └── CustomResponse.php
│   ├── Router.php
│   └── (other application files)
└── public/
    └── (static assets)
```

## Web Server Configuration

### Apache Configuration

Create a `.htaccess` file in your project root:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx Configuration

Add to your Nginx server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### PHP Built-in Server (Development)

```bash
# Start development server
php -S localhost:8000

# With custom document root
php -S localhost:8000 -t public/
```

## Environment Setup

### Development Environment

1. **Clone or download** the framework
2. **Install dependencies**: `composer install`
3. **Start server**: `php -S localhost:8000`
4. **Test installation**: Visit `http://localhost:8000`

### Production Environment

1. **Install via Composer** in production
2. **Configure web server** (Apache/Nginx)
3. **Set up SSL** certificates
4. **Configure PHP** settings for production
5. **Set up monitoring** and logging

## PHP Configuration

### Required PHP Settings

```ini
; php.ini settings
extension=json
memory_limit=128M
max_execution_time=30
upload_max_filesize=10M
post_max_size=10M
```

### Recommended PHP Settings

```ini
; Production settings
display_errors=Off
log_errors=On
error_log=/var/log/php_errors.log
opcache.enable=1
opcache.memory_consumption=128
```

## Composer Configuration

### composer.json Example

```json
{
    "name": "my-company/my-donner-app",
    "type": "project",
    "require": {
        "php": ">=8.1",
        "ji/donner": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "scripts": {
        "start": "php -S localhost:8000",
        "test": "phpunit"
    }
}
```

## Docker Setup (Optional)

### Dockerfile

```dockerfile
FROM php:8.1-fpm

# Install extensions
RUN docker-php-ext-install json

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html
```

### docker-compose.yml

```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    command: php -S 0.0.0.0:8000
```

## Verification

### Test Installation

Create a simple test file:

```php
<?php
// test.php
require_once 'vendor/autoload.php';

use Donner\BasicRouter;
use Donner\Response\MixedResponse;
use Donner\Utils\HTTPCode;

class TestController extends \Donner\Controller\AbstractController {
    public const URI = '/test';
    public const ALLOWED_METHOD = self::METHOD_GET;
    
    public function resolve(): \Donner\Response\ResponseInterface {
        return new MixedResponse(['status' => 'OK'], HTTPCode::OK);
    }
}

BasicRouter::create()
    ->addController(new TestController())
    ->run();
```

Visit `http://localhost:8000/test` to verify the installation.

## Troubleshooting

### Common Issues

#### "Class not found" errors
- Ensure Composer autoloader is included
- Check namespace declarations
- Run `composer dump-autoload`

#### "Route not found" errors
- Verify URI patterns in controllers
- Check HTTP method configuration
- Ensure web server rewrite rules are working

#### File upload issues
- Check PHP upload settings
- Verify file permissions
- Ensure upload directory exists

#### Performance issues
- Enable OPcache
- Use a proper web server (not built-in)
- Optimize Composer autoloader

### Getting Help

If you encounter issues:

1. Check the [Troubleshooting Guide](troubleshooting.md)
2. Review [Common Issues](common-issues.md)
3. Open an issue on GitHub
4. Contact support at jeanisahakyan@gmail.com

## Next Steps

After successful installation:

1. Follow the [Getting Started Guide](getting-started.md)
2. Explore [Examples & Tutorials](examples.md)
3. Read the [API Reference](api-reference.md)
4. Check [Best Practices](best-practices.md)
