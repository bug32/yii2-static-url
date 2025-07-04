# Yii2 Static URL Extension

Yii2-расширение для управления статическими URL с хранением в базе данных и автоматической интеграцией с маршрутизацией URL.

## Возможности

- **Хранение в базе данных**: Сохраняет статические URL с контроллером, действием и параметрами.
- **Автоматическая маршрутизация**: Бесшовная интеграция с urlManager Yii2.
- **Кэширование**: Встроенное кэширование для оптимальной производительности.
- **Админка**: Полноценный CRUD-интерфейс для управления статическими URL.
- **Консольные команды**: Инструменты для управления через консоль.
- **Вспомогательные функции**: Удобные хелперы для создания статических URL.
- **SEO-дружелюбность**: Создание чистых, SEO-оптимизированных URL.
- **Гибкие параметры**: Поддержка JSON-параметров и query-строк.

## Установка

### Через Composer

```bash
composer require bug32/yii2-static-url
```

### Ручная установка

1. Скачайте расширение.
2. Распакуйте в директорию `extensions/` вашего проекта.
3. Добавьте в секцию autoload вашего composer.json:

```json
{
    "autoload": {
        "psr-4": {
            "bug32\\staticUrl\\": "extensions/static-url/src/"
        }
    }
}
```

4. Выполните `composer dump-autoload`.

## Конфигурация

### Базовая настройка

Добавьте расширение в конфиг приложения:

```php
// frontend/config/main.php или backend/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
        ],
    ],
    // ... остальной конфиг
];
```

### Настройка для backend (опционально)

Для административного интерфейса:

```php
// backend/config/main.php
return [
    'modules' => [
        'static-url' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
        ],
    ],
    // ... остальной конфиг
];
```

---

### Расширенная настройка

Вы можете тонко настроить модуль для разных окружений:

```php
// extensions/static-url/src/config.php
return [
    'components' => [
        'staticUrlRule' => [
            'class' => 'bug32\\staticUrl\\components\\StaticUrlRule',
            // 'cacheEnabled' => true,           // Включить/выключить кэширование
            // 'cacheDuration' => 3600,          // Время жизни кэша в секундах
            // 'autoClearCache' => true,         // Автоочистка кэша при изменениях
        ],
    ],
    'modules' => [
        'static-url' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            // 'adminRoute' => 'static-url/backend',
            // 'enableConsoleCommands' => true,  // Включить консольные команды
            // 'enableAdminInterface' => true,   // Включить админку
            // 'defaultStatus' => 10,            // Статус по умолчанию для новых URL
            // 'urlValidationPattern' => '/^[a-z0-9\-_\/]+$/',
        ],
    ],
];
```

#### Пример: Конфиг для production

```php
// environments/prod/common/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            'enableAdminInterface' => false, // Отключить админку в проде
            'cacheEnabled' => true,
            'cacheDuration' => 7200, // 2 часа
        ],
    ],
];
```

#### Пример: Конфиг для разработки

```php
// environments/dev/common/config/main.php
return [
    'bootstrap' => [
        'staticUrl' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
            'enableAdminInterface' => true,
            'cacheEnabled' => false, // Отключить кэш для разработки
            'autoClearCache' => true,
        ],
    ],
];
```

#### Пример: Консольный конфиг

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

#### Пример: интеграция с urlManager

```php
// frontend/config/main.php
return [
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Статические URL будут добавлены автоматически в начало
                // Остальные правила остаются без изменений
                'posts/<id:\d+>' => 'posts/view',
                'ships/<slug>' => 'ship/view',
            ],
        ],
    ],
];
```

---

#### Доступные параметры

- `enableAdminInterface` (bool) — Включить/выключить админку
- `enableConsoleCommands` (bool) — Включить/выключить консольные команды
- `adminRoute` (string) — Маршрут для админки
- `defaultStatus` (int) — Статус по умолчанию для новых URL
- `urlValidationPattern` (string) — Регулярка для проверки URL
- `cacheEnabled` (bool) — Включить/выключить кэширование
- `cacheDuration` (int) — Время жизни кэша в секундах
- `autoClearCache` (bool) — Автоочистка кэша при изменениях

---

## Миграция базы данных

Выполните миграцию для создания нужной таблицы:

```bash
php yii migrate --migrationPath=@vendor/bug32/yii2-static-url/migrations
```

## Использование

### В представлениях и контроллерах

```php
use bug32\staticUrl\helpers\StaticUrlHelper;

// Создать статический URL
$url = StaticUrlHelper::to('site/about'); // Вернет 'about-us'
$url = StaticUrlHelper::to('site/contact'); // Вернет 'contact'

// Создать абсолютный URL
$absoluteUrl = StaticUrlHelper::toAbsolute('site/about');

// Проверить, является ли URL статическим
if (StaticUrlHelper::isStaticUrl('about-us')) {
    echo 'Это статический URL';
}

// Получить маршрут по статическому URL
$route = StaticUrlHelper::getRouteForUrl('about-us'); // Вернет 'site/about'
```

### Консольные команды

```bash
# Список всех статических URL
php yii static-url/index

# Очистить кэш
php yii static-url/clear-cache

# Создать статический URL
php yii static-url/create "about-us" "site" "about" "{}"

# Удалить статический URL
php yii static-url/delete 1
```

### Админка

Админка доступна по адресу: `your-domain.com/static-url/backend/`

## Структура базы данных

| Колонка     | Тип           | Описание                        |
|-------------|---------------|---------------------------------|
| id          | int           | Первичный ключ                  |
| url         | varchar(255)  | Статический URL (уникальный)    |
| controller  | varchar(100)  | Имя контроллера                 |
| action      | varchar(100)  | Имя действия                    |
| params      | json          | JSON-параметры                  |
| status      | smallint      | Статус (10=активен, 0=неактивен)|
| created_at  | timestamp     | Дата создания                   |
| updated_at  | timestamp     | Дата обновления                 |

## Примеры

### Базовый статический URL

```php
// Запись в базе
url: 'about-us'
controller: 'site'
action: 'about'
params: '{}'

// Использование
StaticUrlHelper::to('site/about'); // Вернет 'about-us'
```

### Статический URL с параметрами

```php
// Запись в базе
url: 'post/123'
controller: 'posts'
action: 'view'
params: '{"id": 123}'

// Использование
StaticUrlHelper::to('posts/view', ['id' => 123]); // Вернет 'post/123'
```

### Статический URL с дополнительными параметрами

```php
// Запись в базе
url: 'post/123'
controller: 'posts'
action: 'view'
params: '{"id": 123}'

// Использование
StaticUrlHelper::to('posts/view', ['id' => 123, 'tab' => 'details']); 
// Вернет 'post/123?tab=details'
```

## API

### StaticUrlHelper

#### `to(string $route, array $params = [], bool $scheme = false): string`

Создает статический URL для указанного маршрута.

- `$route`: Маршрут контроллер/действие (например, 'site/about')
- `$params`: Дополнительные параметры
- `$scheme`: Создавать абсолютный URL

#### `toAbsolute(string $route, array $params = []): string`

Создает абсолютный статический URL.

#### `isStaticUrl(string $url): bool`

Проверяет, является ли указанный URL статическим.

#### `getRouteForUrl(string $url): ?string`

Получает маршрут для статического URL.

#### `getAllStaticUrls(): array`

Получает все активные статические URL.

### StaticUrl (модель)

#### `getParamsArray(): array`

Получает параметры как массив.

#### `setParamsArray(array $params): void`

Устанавливает параметры из массива.

#### `getRoute(): string`

Получает полный маршрут (контроллер/действие).

#### `getStatusList(): array`

Получает список доступных статусов.

## Производительность

Расширение использует in-memory кэширование для статических URL для максимальной производительности. Кэш автоматически очищается при изменениях через админку или консольные команды.

## Безопасность

- Валидация URL гарантирует, что разрешены только безопасные символы.
- Уникальность URL предотвращает дублирование.
- Статус позволяет временно отключать URL.
- JSON-параметры валидируются при сохранении.

## Вклад

1. Форкните репозиторий.
2. Создайте ветку для новой фичи.
3. Внесите изменения.
4. Добавьте тесты.
5. Оформите pull request.

## Лицензия

Расширение распространяется под лицензией MIT. Подробнее см. [LICENSE](LICENSE).

## Поддержка

- [Issues](https://github.com/bug32/yii2-static-url/issues)
- [Email](mailto:info@bug32.online)
- [Website](https://bug32.online)

## Changelog

### 1.0.0
- Первый релиз
- Базовая работа со статическими URL
- Админка
- Консольные команды
- Вспомогательные функции
- Поддержка кэширования 