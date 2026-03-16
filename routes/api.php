<?php

namespace Route\routes;

use App\Service\ClientService;
use App\Service\GatewayService;
use App\Service\ProductService;
use App\Service\TransactionService;
use App\Service\UserService;
use Route\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Proxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {

    $app->post('/login', function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        $userService = new UserService();

        $userService->login($email, $password);

        $response->getBody()->write(json_encode($userService->response));
        $response = $response->withStatus($userService->status);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->group('/transaction', function (Proxy $group) {
        $group->post('/', function (Request $request, Response $response) {
            $body = json_decode($request->getBody()->getContents(), true);
            $client = $body['client'] ?? null;
            $products = $body['products'] ?? null;
            $cardNumbers = $body['cardNumbers'] ?? null;
            $cvv = $body['cvv'] ?? null;

            $transactionService = new TransactionService();
            $transactionService->create($client, $products, $cardNumbers, $cvv);

            $response->getBody()->write(json_encode($transactionService->response));
            $response = $response->withStatus($transactionService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->post('/charge_back/{id}', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;

            $transactionService = new TransactionService();

            !$transactionService->chargeBack($id);

            $response->getBody()->write(json_encode($transactionService->response));
            $response = $response->withStatus($transactionService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/user', function (Proxy $group) {
        $group->get('/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $userService = new UserService();

            $userService->read($id);

            $response->getBody()->write(json_encode($userService->response));
            $response = $response->withStatus($userService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->post('/', function (Request $request, Response $response) {
            $body = json_decode($request->getBody()->getContents(), true);
            $email = $body['email'] ?? null;
            $password = $body['password'] ?? null;
            $role = $body['role'] ?? null;
            $userService = new UserService();

            $userService->create($email, $password, $role);

            $response->getBody()->write(json_encode($userService->response));
            $response = $response->withStatus($userService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->put('/[{id}]', function (Request $request, Response $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            $id = $args['id'] ?? null;
            $email = $body['email'] ?? null;
            $password = $body['password'] ?? null;
            $role = $body['role'] ?? null;

            $userService = new UserService();

            $userService->update($id, $email, $password, $role);

            $response->getBody()->write(json_encode($userService->response));
            $response = $response->withStatus($userService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->delete('/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $userService = new UserService();

            $userService->delete($id);

            $response->getBody()->write(json_encode($userService->response));
            $response = $response->withStatus($userService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->addMiddleware(new SessionMiddleware());

    $app->group('/product', function (Proxy $group) {
        $group->get('/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $productService = new ProductService();

            $productService->read($id);

            $response->getBody()->write(json_encode($productService->response));
            $response = $response->withStatus($productService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->post('/', function (Request $request, Response $response) {
            $body = json_decode($request->getBody()->getContents(), true);
            $name = $body['name'] ?? null;
            $amount = $body['amount'] ?? null;
            $productService = new ProductService();

            $productService->create($name, $amount);

            $response->getBody()->write(json_encode($productService->response));
            $response = $response->withStatus($productService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->put('/[{id}]', function (Request $request, Response $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            $id = $args['id'] ?? null;
            $name = $body['name'] ?? null;
            $amount = $body['amount'] ?? null;
            $productService = new ProductService();

            $productService->update($id, $name, $amount);

            $response->getBody()->write(json_encode($productService->response));
            $response = $response->withStatus($productService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->delete('/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $productService = new ProductService();

            $productService->delete($id);

            $response->getBody()->write(json_encode($productService->response));
            $response = $response->withStatus($productService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->addMiddleware(new SessionMiddleware());

    $app->group('/client', function (Proxy $group) {
        $group->get('/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $clientService = new ClientService();

            $clientService->read($id);

            $response->getBody()->write(json_encode($clientService->response));
            $response = $response->withStatus($clientService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/transactions/[{id}]', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? null;
            $clientService = new ClientService();

            if (!$clientService->read($id)) {
                $response->getBody()->write(json_encode($clientService->response));
                $response = $response->withStatus($clientService->status);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $transactionService = new TransactionService();
            $transactionService->readByClient($id);

            if ($transactionService->status !== 200) {
                $Json = $transactionService->response;
            } else {
                $Json = ['client' => $clientService->response['client'], 'transactions' => $transactionService->response['transactions']];
            }

            $response->getBody()->write(json_encode($Json));
            $response = $response->withStatus($transactionService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->addMiddleware(new SessionMiddleware());

    $app->group('/gateway', function (Proxy $group) {
        $group->put('/active/[{id}]', function (Request $request, Response $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            $is_active = $body['is_active'] ?? null;
            $id = $args['id'] ?? null;

            $gatewayService = new GatewayService();

            $gatewayService->updateActive($id, $is_active);

            $response->getBody()->write(json_encode($gatewayService->response));
            $response = $response->withStatus($gatewayService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->put('/priority/{id}', function (Request $request, Response $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            $priority = $body['priority'] ?? null;
            $id = $args['id'] ?? null;

            $gatewayService = new GatewayService();

            $gatewayService->updatePriority($id, $priority);

            $response->getBody()->write(json_encode($gatewayService->response));
            $response = $response->withStatus($gatewayService->status);
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->addMiddleware(new SessionMiddleware());
};