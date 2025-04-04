<?php

declare(strict_types=1);

namespace Hypervel\Cookie\Middleware;

use Hyperf\Collection\Arr;
use Hyperf\Context\Context;
use Hypervel\Cookie\Contracts\Cookie as CookieContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddQueuedCookiesToResponse implements MiddlewareInterface
{
    public function __construct(
        protected CookieContract $cookie
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (! $cookies = $this->cookie->getQueuedCookies()) {
            return $response;
        }

        foreach (Arr::flatten($cookies) as $cookie) {
            $response = $response->withCookie($cookie);
        }

        Context::set(ResponseInterface::class, $response);

        return $response;
    }
}
