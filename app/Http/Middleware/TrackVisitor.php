<?php

namespace App\Http\Middleware;

use App\Models\VisitorVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $visitorId = $request->cookie('site_visitor');
        $needsCookie = ! is_string($visitorId) || ! Str::isUuid($visitorId);

        if ($needsCookie) {
            $visitorId = (string) Str::uuid();
        }

        $response = $next($request);

        if ($request->isMethod('GET') && $response->isSuccessful()) {
            $productSlug = $request->routeIs('products.show')
                ? $request->route('slug')
                : null;

            VisitorVisit::query()->create([
                'visitor_hash' => hash('sha256', $visitorId),
                'product_slug' => $productSlug,
                'product_name' => $request->attributes->get('analytics_product_name'),
            ]);
        }

        if ($needsCookie && $response->isSuccessful()) {
            $response->withCookie(Cookie::make(
                'site_visitor',
                $visitorId,
                60 * 24 * 365,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax',
            ));
        }

        return $response;
    }
}
