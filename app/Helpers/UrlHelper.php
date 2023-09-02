<?php

namespace App\Helpers;

class UrlHelper
{
    /**
     * Build company Nova Admin url for given params
     * @param string $companyId
     * @param string|null $path
     * @param array $query
     * @param string|null $schema
     * @return string
     */
    public static function getCompanyAdminUrl(
        string $companyId,
        string $path = null,
        array $query = [],
        string $schema = null
    ): string {
        $host = self::addSubdomainToUrl($companyId, config('app.url'));

        return self::buildUrl($host, $path, $query, $schema);
    }

    /**
     * Build admins Nova Admin url for given params
     * @param string|null $path
     * @param array $query
     * @param string|null $schema
     * @return string
     */
    public static function getAdminUrl(
        string $path = null,
        array $query = [],
        string $schema = null
    ): string {
        $host = config('app.url');

        return self::buildUrl($host, $path, $query, $schema);
    }

    /**
     * Build API url for given company
     * @return string|null
     */
    public static function getApiUrl(): ?string
    {
        $apiPath = config('app.apiUrl');

        return ltrim($apiPath, '/\\');
    }

    /**
     * Returns current request scheme
     * @return string
     */
    public static function getCurrentScheme(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http';
    }

    /**
     * Returns company slug from current subdomain name if matches
     * otherwise returns null (if subdomain doesn't match)
     * @return string|null
     */
    public static function getCompanySlugFromCurrentHost(): ?string
    {
        return self::getFirstSubdomainOfCurrentHost();
    }

    public static function isHasCompanySlugInCurrentHost(): bool
    {
        return self::getFirstSubdomainOfCurrentHost() !== null;
    }

    /**
     * Returns first part of current host before first dot
     * @return string|null
     */
    public static function getFirstSubdomainOfCurrentHost(): ?string
    {
        $subdomains = $_SERVER['HTTP_HOST'];
        if ($subdomains === config('app.url')) {
            return null;
        }
        $subs = explode('.', $subdomains);
        if (count($subs) === 2) {
            return null;
        }

        return $subs[0];
    }

    public static function buildUrl(
        string $host,
        string $path = null,
        array $query = [],
        string $schema = null
    ): string {
        $url = rtrim($host, '/');

        if (! empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }

        if (! empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        if ($schema !== null && ! str_starts_with($url, 'https://') && ! str_starts_with($url, 'http://')) {
            $url = $schema . '://' . $url;
        }

        return $url;
    }

    public static function addSubdomainToUrl($subdomain, $url): string
    {
        $domainStartPosition = 0;

        if (str_contains($url, '://')) {
            $domainStartPosition = strpos($url, '//') + 2;
        }

        return substr_replace($url, $subdomain . '.', $domainStartPosition, 0);
    }
}
