# Yii2 Static URL Extension

[![Latest Stable Version](https://poser.pugx.org/bug32/yii2-static-url/v/stable)](https://packagist.org/packages/bug32/yii2-static-url)
[![Total Downloads](https://poser.pugx.org/bug32/yii2-static-url/downloads)](https://packagist.org/packages/bug32/yii2-static-url)
[![License](https://poser.pugx.org/bug32/yii2-static-url/license)](https://packagist.org/packages/bug32/yii2-static-url)

Yii2 extension for managing static URLs with database storage and automatic URL routing integration.

## Features

- **Database Storage**: Store static URLs in database with controller, action, and parameters
- **Automatic Routing**: Seamless integration with Yii2's urlManager
- **Caching**: Built-in caching for optimal performance
- **Admin Interface**: Full CRUD interface for managing static URLs
- **Console Commands**: Command-line tools for management
- **Helper Functions**: Easy-to-use helper for creating static URLs
- **SEO Friendly**: Create clean, SEO-friendly URLs
- **Flexible Parameters**: Support for JSON parameters and query strings

## Installation

### Via Composer

```bash
composer require bug32/yii2-static-url
```

### Manual Installation

```
composer require bug32/yii2-static-url
```

## Configuration

### Basic Setup

Add the extension to your application configuration:

```php
// frontend/config/main.php or backend/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
        ],
    ],
    // ... other config
];
```

### Backend Setup (Optional)

For backend management interface:

```php
// backend/config/main.php
return [
    'modules' => [
        'static-url' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
        ],
    ],
    // ... other config
];
```

---

### Advanced Configuration

You can fine-tune the module for different environments and needs:

```php
// extensions/static-url/src/config.php
return [
    'components' => [
        'staticUrlRule' => [
            'class' => 'bug32\\staticUrl\\components\\StaticUrlRule',
            // 'cacheEnabled' => true,           // Enable/disable caching
            // 'cacheDuration' => 3600,          // Cache lifetime in seconds
            // 'autoClearCache' => true,         // Auto-clear cache on changes
        ],
    ],
    'modules' => [
        'static-url' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            // 'adminRoute' => 'static-url/backend',
            // 'enableConsoleCommands' => true,  // Enable console commands
            // 'enableAdminInterface' => true,   // Enable admin interface
            // 'defaultStatus' => 10,            // Default status for new URLs
            // 'urlValidationPattern' => '/^[a-z0-9\-_\/]+$/',
        ],
    ],
];
```

#### Example: Production config

```php
// environments/prod/common/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            'enableAdminInterface' => false, // Disable admin in production
            'cacheEnabled' => true,
            'cacheDuration' => 7200, // 2 hours
        ],
    ],
];
```

#### Example: Development config

```php
// environments/dev/common/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            'enableAdminInterface' => true,
            'cacheEnabled' => false, // Disable cache for development
            'autoClearCache' => true,
        ],
    ],
];
```

#### Example: Console config

```php
// console/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            'enableConsoleCommands' => true,
            'enableAdminInterface' => false,
        ],
    ],
];
```

#### Example: urlManager integration

```php
// frontend/config/main.php
return [
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Static URLs will be added automatically at the beginning
                // Other rules remain unchanged
                '/' => 'site/index',
            ],
        ],
    ],
];
```

---

#### Available parameters

- `enableAdminInterface` (bool) — Enable/disable admin interface
- `enableConsoleCommands` (bool) — Enable/disable console commands
- `adminRoute` (string) — Route for admin interface
- `defaultStatus` (int) — Default status for new URLs
- `urlValidationPattern` (string) — Regex for URL validation
- `cacheEnabled` (bool) — Enable/disable caching
- `cacheDuration` (int) — Cache lifetime in seconds
- `autoClearCache` (bool) — Auto-clear cache on changes

---

## Database Migration

Run the migration to create the required table:

```bash
php yii migrate --migrationPath=@vendor/bug32/yii2-static-url/migrations
```

## Usage

### In Views and Controllers

```php
use bug32\staticUrl\helpers\StaticUrlHelper;

// Create static URL
$url = StaticUrlHelper::to('site/about'); // Returns 'about-us'
$url = StaticUrlHelper::to('site/contact'); // Returns 'contact'

// Create absolute URL
$absoluteUrl = StaticUrlHelper::toAbsolute('site/about');

// Check if URL is static
if (StaticUrlHelper::isStaticUrl('about-us')) {
    echo 'This is a static URL';
}

// Get route for static URL
$route = StaticUrlHelper::getRouteForUrl('about-us'); // Returns 'site/about'
```

### Console Commands

```bash
# List all static URLs
php yii static-url/index

# Clear cache
php yii static-url/clear-cache

# Create static URL
php yii static-url/create "about-us" "site" "about" "{}"

# Delete static URL
php yii static-url/delete 1
```

### Admin Interface

Access the admin interface at: `your-domain.com/static-url/backend/`

## Database Structure

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary key |
| url | varchar(255) | Static URL (unique) |
| controller | varchar(100) | Controller name |
| action | varchar(100) | Action name |
| params | json | JSON parameters |
| status | smallint | Status (10=active, 0=inactive) |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Update timestamp |

## Examples

### Basic Static URL

```php
// Database record
url: 'about-us'
controller: 'site'
action: 'about'
params: '{}'

// Usage
StaticUrlHelper::to('site/about'); // Returns 'about-us'
```

### Static URL with Parameters

```php
// Database record
url: 'post/123'
controller: 'posts'
action: 'view'
params: '{"id": 123}'

// Usage
StaticUrlHelper::to('posts/view', ['id' => 123]); // Returns 'post/123'
```

### Static URL with Additional Parameters

```php
// Database record
url: 'post/123'
controller: 'posts'
action: 'view'
params: '{"id": 123}'

// Usage
StaticUrlHelper::to('posts/view', ['id' => 123, 'tab' => 'details']); 
// Returns 'post/123?tab=details'
```

## API Reference

### StaticUrlHelper

#### `to(string $route, array $params = [], bool $scheme = false): string`

Creates a static URL for the given route.

- `$route`: Controller/action route (e.g., 'site/about')
- `$params`: Additional parameters
- `$scheme`: Whether to create absolute URL

#### `toAbsolute(string $route, array $params = []): string`

Creates an absolute static URL.

#### `isStaticUrl(string $url): bool`

Checks if the given URL is a static URL.

#### `getRouteForUrl(string $url): ?string`

Gets the route for a static URL.

#### `getAllStaticUrls(): array`

Gets all active static URLs.

### StaticUrl Model

#### `getParamsArray(): array`

Gets parameters as array.

#### `setParamsArray(array $params): void`

Sets parameters from array.

#### `getRoute(): string`

Gets full route (controller/action).

#### `getStatusList(): array`

Gets list of available statuses.

## Performance

The extension uses in-memory caching for static URLs to ensure optimal performance. The cache is automatically cleared when URLs are modified through the admin interface or console commands.

## Security

- URL validation ensures only safe characters are allowed
- Unique constraint prevents duplicate URLs
- Status field allows temporary disabling of URLs
- JSON parameters are validated on save

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This extension is released under the MIT License. See [LICENSE](LICENSE) for details.

## Support

- [Issues](https://github.com/bug32/yii2-static-url/issues)
- [Email](mailto:info@bug32.online)
- [Website](https://bug32.online)

## Changelog

### 1.0.0
- Initial release
- Basic static URL functionality
- Admin interface
- Console commands
- Helper functions
- Caching support 