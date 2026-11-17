<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Domain;

/**
 * Which workspace (company) the current host belongs to, and of which type.
 *
 * Every module only registers its routes for its own company type (see each module's
 * RouteServiceProvider and Workspace::serves()), so a restaurant domain and a shop domain
 * can both use URLs such as "/", "/login" and "/admin/dashboard".
 *
 * Hosts without a (verified) workspace get the 410 "create your workspace" page at "/"
 * (App\Http\Middleware\RequireWorkspace, resources/views/errors/410.blade.php).
 */
class Workspace
{
    /** @var array<string, Domain|null> */
    private static array $domains = [];

    /** Route names reachable on a host without a workspace (the sign-up pages). */
    private static array $withoutWorkspace = [];

    /** Route names reachable while the workspace's sign-up is not finished (verify email, logout). */
    private static array $whileUnverified = [];

    public static function domain(?string $host = null): ?Domain
    {
        $host ??= request()->getHost();

        if (! array_key_exists($host, self::$domains)) {
            self::$domains[$host] = Domain::with('company')->where('domain', $host)->first();
        }

        return self::$domains[$host];
    }

    public static function company(?string $host = null): ?Company
    {
        return self::domain($host)?->company;
    }

    /** "restaurant", "shop", or null when the host has no company. */
    public static function type(?string $host = null): ?string
    {
        return self::company($host)?->company_type;
    }

    /** True when the host has no usable workspace yet: no company, or its sign-up isn't verified. */
    public static function missing(?string $host = null): bool
    {
        $company = self::company($host);

        return ! $company || ! $company->isVerified();
    }

    /**
     * Should the module for $type register its routes for this request? Only on its own
     * domains (and in the console, for route:list and queued mail building URLs).
     *
     * Hosts without a company only get each module's sign-up routes (see isUnclaimedHost()):
     * loading two modules' full route files there would make their equal URLs ("/", "/admin/login")
     * replace each other, and the replaced route's name would no longer resolve.
     */
    public static function serves(string $type): bool
    {
        return app()->runningInConsole() || self::type() === $type;
    }

    /** True for a web request on a host that has no company at all (sign-up / 410 pages only). */
    public static function isUnclaimedHost(): bool
    {
        return ! app()->runningInConsole() && self::company() === null;
    }

    /** @param  string[]  $routeNames  patterns, e.g. "shop.register.*" */
    public static function allowWithoutWorkspace(array $routeNames): void
    {
        self::$withoutWorkspace = array_merge(self::$withoutWorkspace, $routeNames);
    }

    /** @param  string[]  $routeNames  patterns, e.g. "admin.verify_email.*" */
    public static function allowWhileUnverified(array $routeNames): void
    {
        self::$whileUnverified = array_merge(self::$whileUnverified, $routeNames);
    }

    public static function routesWithoutWorkspace(): array
    {
        return self::$withoutWorkspace;
    }

    public static function routesWhileUnverified(): array
    {
        return self::$whileUnverified;
    }

    /** Forget cached lookups (e.g. right after a sign-up created the domain). */
    public static function flush(): void
    {
        self::$domains = [];
    }
}
