<?php

namespace App\Service\PaymentAPI;

class Gateway2Service
{

    public array $header;
    public string $url = 'http://192.168.1.2:3002';

    public int $status;

    public array $response;

    public ServerService $serverService;


    public function __construct()
    {
        $this->serverService = new ServerService();
        $this->serverService->url = $this->url;
        $this->header = [
            'Gateway-Auth-Token: tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret: 3d15e8ed6131446ea7e3456728b1211f'
        ];
    }

    public function listTransactions(?string $id): bool
    {

        if (!is_string($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id must be string'];
            return false;
        }

        if (!$this->serverService->get('/transacoes', $this->header)) {
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
        $response = $this->serverService->post('/transacoes', [
            'valor' => $amount,
            'nome' => $name,
            'email' => $email,
            'numeroCartao' => $cardNumber,
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

        $response = $this->serverService->post('/transacoes/reembolso', [
            'id' => $id,
        ], $this->header);

        $this->status = $this->serverService->status;
        $this->response = $this->serverService->response;
        return $response;

    }
}