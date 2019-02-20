<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Interop\Container\ServiceProviderInterface;

use Quanta\Http\MiddlewareQueue;
use Quanta\Http\DispatchedMiddleware;
use Services\Http\InnerMostRequestHandler;
use Services\Http\HttpDispatcherServiceProvider;

describe('HttpDispatcherServiceProvider', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->provider = new HttpDispatcherServiceProvider;

    });

    it('should implement ServiceProviderInterface', function () {

        expect($this->provider)->toBeAnInstanceOf(ServiceProviderInterface::class);

    });

    describe('->getFactories()', function () {

        beforeEach(function () {

            $this->factories = $this->provider->getFactories();

        });

        it('should return an array of length 3', function () {

            expect($this->factories)->toBeAn('array');
            expect($this->factories)->toHaveLength(3);

        });

        it('should provide a http.middleware.queue entry returning an empty array', function () {

            $factory = $this->factories['http.middleware.queue'];

            $test = $factory($this->container->get());

            expect($test)->toEqual([]);

        });

        it('should provide a MiddlewareInterface entry returning a MiddlewareQueue from the http.middleware.queue entry array', function () {

            $middleware1 = mock(MiddlewareInterface::class);
            $middleware2 = mock(MiddlewareInterface::class);
            $middleware3 = mock(MiddlewareInterface::class);

            $this->container->get->with('http.middleware.queue')->returns([
                $middleware1->get(),
                $middleware2->get(),
                $middleware3->get(),
            ]);

            $factory = $this->factories[MiddlewareInterface::class];

            $test = $factory($this->container->get());

            expect($test)->toEqual(new MiddlewareQueue(...[
                $middleware1->get(),
                $middleware2->get(),
                $middleware3->get(),
            ]));

        });

        it('should provide a RequestHandlerInterface entry returning a InnerMostRequestHandler', function () {

            $factory = $this->factories[RequestHandlerInterface::class];

            $test = $factory($this->container->get());

            expect($test)->toEqual(new InnerMostRequestHandler);

        });

    });

    describe('->getExtensions()', function () {

        beforeEach(function () {

            $this->extensions = $this->provider->getExtensions();

        });

        it('should return an array of length 1', function () {

            expect($this->extensions)->toBeAn('array');
            expect($this->extensions)->toHaveLength(1);

        });

        it('should provide a RequestHandlerInterface extension returning a DispatchedMiddleware from the previous RequestHandlerInterface and the MiddlewareInterface entry', function () {

            $middleware = mock(MiddlewareInterface::class);
            $handler = mock(RequestHandlerInterface::class);

            $this->container->get->with(MiddlewareInterface::class)->returns($middleware);

            $extension = $this->extensions[RequestHandlerInterface::class];

            $test = $extension($this->container->get(), $handler->get());

            expect($test)->toEqual(new DispatchedMiddleware(
                $handler->get(),
                $middleware->get()
            ));

        });

    });

});
