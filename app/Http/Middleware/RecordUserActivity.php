<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecordUserActivity
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldRecord($request, $response)) {
            activity()
                ->causedBy($request->user())
                ->event('viewed')
                ->withProperties([
                    'route' => $request->route()?->getName(),
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
                    'status' => $response->getStatusCode(),
                ])
                ->log($this->description($request));
        }

        return $response;
    }

    private function shouldRecord(Request $request, Response $response): bool
    {
        $route = $request->route()?->getName();

        if (! $request->user() || ! $request->isMethod('GET') || $response->getStatusCode() >= 400) {
            return false;
        }

        if (! $route || in_array($route, ['company.logo.show', 'home'], true)) {
            return false;
        }

        return $request->inertia()
            || $route === 'dashboard'
            || Str::endsWith($route, ['.index', '.edit', '.create']);
    }

    private function description(Request $request): string
    {
        return match ($request->route()?->getName()) {
            'dashboard' => 'Dashboard viewed',
            'admin.users.index' => 'Users and access viewed',
            'admin.audit-logs.index' => 'Audit logs viewed',
            'onboarding.company.create' => 'Company onboarding form viewed',
            'profile.edit' => 'Profile settings viewed',
            'security.edit' => 'Security settings viewed',
            'appearance.edit' => 'Appearance settings viewed',
            default => Str::headline(str_replace('.', ' ', (string) $request->route()?->getName())).' viewed',
        };
    }
}
