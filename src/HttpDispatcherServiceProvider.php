<?php declare(strict_types=1);

namespace Services\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Interop\Container\ServiceProviderInterface;

use Quanta\Http\MiddlewareQueue;
use Quanta\Http\DispatchedMiddleware;

final class HttpDispatcherServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            'http.middleware.queue' => [self::class, 'getMiddlewareQueue'],
            MiddlewareInterface::class => [self::class, 'getMiddlewareInterface'],
            RequesthandlerInterface::class => [self::class, 'getRequestHandlerInterface'],
        ];
    }

    public function getExtensions()
    {
        return [
            RequestHandlerInterface::class => [self::class, 'extendRequestHandlerInterface'],
        ];
    }

    public static function getMiddlewareQueue(ContainerInterface $container): array
    {
        return [];
    }

    public static function getMiddlewareInterface(ContainerInterface $container): MiddlewareInterface
    {
        $queue = $container->get('http.middleware.queue');

        return new MiddlewareQueue(...$queue);
    }

    public static function getRequestHandlerInterface(ContainerInterface $container): RequestHandlerInterface
    {
        return new InnerMostRequestHandler;
    }

    public static function extendRequestHandlerInterface(ContainerInterface $container, RequestHandlerInterface $handler): RequestHandlerInterface
    {
        $middleware = $container->get(MiddlewareInterface::class);

        return new DispatchedMiddleware($handler, $middleware);
    }
}
