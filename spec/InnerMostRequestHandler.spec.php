<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

use Services\Http\InnerMostRequestHandler;

describe('InnerMostRequestHandler', function () {

    beforeEach(function () {

        $this->handler = new InnerMostRequestHandler;

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $uri = mock(UriInterface::class);

            $uri->__toString->returns('http://test');

            $this->request = mock(ServerRequestInterface::class);

            $this->request->getMethod->returns('get');
            $this->request->getUri->returns($uri);

        });

        it('should throw a LogicException', function () {

            $test = function () {
                $this->handler->handle($this->request->get());
            };

            expect($test)->toThrow(new LogicException);

        });

        it('should throw an exception containing the request mathod and uri', function () {

            $test = '';

            try {
                $this->handler->handle($this->request->get());
            }

            catch (Throwable $e) {
                $test = $e->getMessage();
            }

            expect($test)->toContain('GET');
            expect($test)->toContain('http://test');

        });

    });

});
