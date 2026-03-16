<?php

namespace Route\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class SessionMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Handler $handler): Response
    {
        session_start();

        if (!isset($_SESSION['role']) || !isset($_SESSION['token'])) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'session not set']));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $headers = $request->getHeaders();
        $token = $headers['Authorization'][0];

        if (str_replace('Bearer ', '', $token) !== $_SESSION['token']) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'token incorrect']));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $permissions = [
            'ADMIN' => [
                'user',
                'product',
                'charge_back',
                'transaction',
                'gateway',
                'client'
            ],
            'MANAGER' => [
                'user',
                'product'
            ],
            'FINANCE' => [
                'product',
                'charge_back'
            ],
            'USER' => [
                'transaction',
                'gateway',
                'client',
            ],
        ];

        if (isset($permissions[$_SESSION['role']])) {
            foreach ($permissions[$_SESSION['role']] as $role) {
                if (str_contains($request->getUri()->getPath(), $role)) {
                    return $handler->handle($request);
                }
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'role not saved']));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response = new Response();
        $response->getBody()->write(json_encode(['error' => 'permissions denied']));
        $response = $response->withStatus(401);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function process(Request $request, Handler $handler): ResponseInterface
    {
        return $this->__invoke($request, $handler);
    }
}