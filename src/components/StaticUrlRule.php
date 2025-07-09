<?php

namespace bug32\staticUrl\components;

use bug32\staticUrl\models\StaticUrl;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class StaticUrlRule extends BaseObject implements UrlRuleInterface
{
    public bool $cacheEnabled  = true;
    public int  $cacheDuration = 3600;
    public bool $autoClearCache = true;

    private static ?array $urlCache = null;
    private static ?array $routeCache     = null;
    private static int    $cacheTimestamp = 0;

    /**
     * @throws \JsonException
     */
    private static function initCache(): void
    {
        $instance = new static();
        if (
            !$instance->cacheEnabled ||
            self::$urlCache === null ||
            self::$routeCache === null ||
            (time() - self::$cacheTimestamp > $instance->cacheDuration)
        ) {
            self::$urlCache = [];
            self::$routeCache = [];
            $staticUrls = StaticUrl::getActiveUrls();
            foreach ($staticUrls as $url) {
                // Для parseRequest
                self::$urlCache[$url['url']] = [
                    'controller' => $url['controller'],
                    'action' => $url['action'],
                    'params' => !empty($url['params']) ? json_decode($url['params'], TRUE, 512, JSON_THROW_ON_ERROR) : [],
                ];
                // Для createUrl
                $route = $url['controller'] . '/' . $url['action'];
                self::$routeCache[$route][] = [
                    'url' => $url['url'],
                    'params' => !empty($url['params']) ? json_decode($url['params'], TRUE, 512, JSON_THROW_ON_ERROR) : [],
                ];
            }
            self::$cacheTimestamp = time();
        }
    }

    public static function clearCache(): void
    {
        self::$urlCache = null;
        self::$routeCache = null;
        self::$cacheTimestamp = 0;
    }

    public function parseRequest($manager, $request)
    {
        self::initCache();
        $pathInfo = $request->getPathInfo();
        if (isset(self::$urlCache[$pathInfo])) {
            $urlData = self::$urlCache[$pathInfo];
            $params = $urlData['params'];
            $queryParams = $request->getQueryParams();
            foreach ($queryParams as $key => $value) {
                if (!array_key_exists($key, $params)) {
                    $params[$key] = $value;
                }
            }
            return [$urlData['controller'] . '/' . $urlData['action'], (array)$params];
        }
        return false;
    }

    public function createUrl($manager, $route, $params)
    {
        self::initCache();
        if (!isset(self::$routeCache[$route])) {
            return false;
        }
        foreach (self::$routeCache[$route] as $item) {
            $requiredParams = $item['params'];
            $hasAllParams = true;
            foreach ($requiredParams as $key => $value) {
                if (!isset($params[$key]) || $params[$key] !== $value) {
                    $hasAllParams = false;
                    break;
                }
            }
            if ($hasAllParams) {
                $queryParams = $params;
                foreach ($requiredParams as $key => $value) {
                    unset($queryParams[$key]);
                }
                $resultUrl = $item['url'];
                if (!empty($queryParams)) {
                    $resultUrl .= '?' . http_build_query($queryParams);
                }
                return $resultUrl;
            }
        }
        return false;
    }
} 