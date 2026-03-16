<?php

namespace App\Service\PaymentAPI;

class Gateway1Service
{

    public const EMAIL = 'dev@betalent.tech';
    public const TOKEN = 'FEC9BB078BF338F464F96B48089EB498';
    public array $header;
    public string $url = 'http://192.168.1.2:3001';

    public int $status;

    public array $response;

    public ServerService $serverService;


    public function __construct()
    {
        $this->serverService = new ServerService();
        $this->serverService->url = $this->url;
    }

    public function login(): bool
    {

        if (!$this->serverService->post('/login', ['email' => self::EMAIL, 'token' => self::TOKEN])) {
            $this->status = $this->serverService->status;
            $this->response = $this->serverService->response;
            return false;
        }

        $this->header = ['Authorization: Bearer ' . $this->serverService->response['token'], 'Content-Type: application/json'];

        return true;
    }

    public function listTransactions(?string $id): bool
    {

        if (!is_string($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id must be string'];
            return false;
        }

        if (!$this->serverService->get('/transactions', $this->header)) {
            $this->status = $this->serverService->status;
            $this->response = $this->serverService->response;
            return false;
        };

        foreach ($this->serverService->response['data'] as $transaction) {
            if ($id == $transaction['id']) {
                $this->status = 200;
                $this->response = $transaction;
                return true;
            }
        }

        $this->status = 404;
        $this->response = ['error' => 'Transaction not found'];
        return false;
    }

    public function createTransaction(int $amount, string $name, string $email, string $cardNumber, string $cvv): bool
    {
        $response = $this->serverService->post('/transactions', [
            'amount' => $amount,
            'name' => $name,
            'email' => $email,
            'cardNumber' => $cardNumber,
            'cvv' => $cvv
        ], $this->header);

        $this->status = $this->serverService->status;
        $this->response = $this->serverService->response;
        return $response;
    }

    public function chargeBack(?string $id): bool
    {
        if (!is_string($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id must be string'];
            return false;
        }

        $response = $this->serverService->post("/transactions/$id/charge_back", [], $this->header);

        $this->status = $this->serverService->status;
        $this->response = $this->serverService->response;
        return $response;

    }
}