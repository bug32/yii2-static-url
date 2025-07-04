<?php

namespace bug32\staticUrl\helpers;

use bug32\staticUrl\models\StaticUrl;
use yii\helpers\Url;

class StaticUrlHelper
{
    public static function to(string $route, array $params = [], bool $scheme = false): string
    {
        $routeParts = explode('/', $route);
        $controller = $routeParts[0];
        $action = $routeParts[1] ?? 'index';
        $staticUrl = StaticUrl::find()
            ->where([
                'controller' => $controller,
                'action' => $action,
                'status' => StaticUrl::STATUS_ACTIVE
            ])
            ->one();
        if ($staticUrl) {
            $staticParams = $staticUrl->getParamsArray();
            $hasAllParams = true;
            foreach ($staticParams as $key => $value) {
                if (!isset($params[$key]) || $params[$key] !== $value) {
                    $hasAllParams = false;
                    break;
                }
            }
            if ($hasAllParams) {
                $queryParams = $params;
                foreach ($staticParams as $key => $value) {
                    unset($queryParams[$key]);
                }
                $resultUrl = $staticUrl->url;
                if (!empty($queryParams)) {
                    $resultUrl .= '?' . http_build_query($queryParams);
                }
                return $scheme ? Url::to($resultUrl, $scheme) : $resultUrl;
            }
        }
        return Url::to([$route] + $params, $scheme);
    }

    public static function toAbsolute(string $route, array $params = []): string
    {
        return self::to($route, $params, true);
    }

    public static function isStaticUrl(string $url): bool
    {
        return StaticUrl::findByPath($url) !== null;
    }

    public static function getRouteForUrl(string $url): ?string
    {
        return StaticUrl::findByPath($url)?->getRoute();
    }

    public static function getAllStaticUrls(): array
    {
        return StaticUrl::getActiveUrls();
    }
} 