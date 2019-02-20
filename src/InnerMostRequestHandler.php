<?php declare(strict_types=1);

namespace Services\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class InnerMostRequestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new \LogicException(
            vsprintf('No middleware returned a response for the request %s %s', [
                strtoupper($request->getMethod()),
                $request->getUri(),
            ])
        );
    }
}
