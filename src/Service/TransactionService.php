<?php

namespace App\Service;

use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Service\PaymentAPI\Gateway1Service;
use App\Service\PaymentAPI\Gateway2Service;
use App\Validators\TransactionValidator;
use Exception;

class TransactionService
{

    public int $status;
    public array $response;

    public function readByClient(?int $id): bool
    {
        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'id not valid', 'id' => $id];
            return false;
        }

        $transactions = Transaction::where('client', $id)?->get()?->toArray();

        if (!isset($transactions[0])) {
            $this->response = ['transactions' => 'not found'];
        } else {
            $this->response = ['transactions' => $transactions];
        }

        $this->status = 200;
        return true;
    }

    public function create(?array $client, ?array $products, ?string $cardNumbers, ?string $cvv): bool
    {

        $clientService = new ClientService();

        if (isset($client['id'])) {

            if (!$clientService->read($client['id'])) {
                $this->status = $clientService->status;
                $this->response = $clientService->response;
                return false;
            }

            $client = $clientService->response['client'];

            if (!isset($client['name'])) {
                $this->status = 404;
                $this->response = ['error' => 'client id not valid'];
                return false;
            }

        } elseif (isset($client['name']) && isset($client['email'])) {

            if (!$clientService->create($client['name'], $client['email'])) {
                $this->status = $clientService->status;
                $this->response = $clientService->response;
                return false;
            } else {
                $client = $clientService->response['client']->toArray();
            }

        } else {

            $this->status = 400;
            $this->response = ['error' => 'client name or email not valid'];
            return false;
        }

        $productService = new ProductService();
        $totalAmount = 0;

        foreach ($products as $product) {

            if (!isset($product['name']) || !isset($product['amount']) || !isset($product['quantity'])) {
                $this->status = 400;
                $this->response = ['error' => 'product data invalid'];
                return false;
            }

            if (!$productService->create($product['name'], $product['amount'])) {
                $this->status = $productService->status;
                $this->response = $productService->response;
                return false;
            }

            $totalAmount += $product['amount'] * $product['quantity'];
        }

        $gatewayService = new GatewayService();

        if (!$gatewayService->getByPriority()) {
            $this->status = $gatewayService->status;
            $this->response = $gatewayService->response;
            return false;
        }

        $gateway = $gatewayService->response['gateway'];

        $transactionValidate = new TransactionValidator();

        if (!$transactionValidate->cardNumber($cardNumbers)) {
            $this->status = 400;
            $this->response = ['error' => 'cardNumbers invalid'];
            return false;
        }

        $cardLastNumber = substr($cardNumbers, -4);

        switch ($gateway['name']) {
            case 'gateway1':
                $paymentService = new Gateway1Service();
                if (!$paymentService->login()) {
                    $this->status = 401;
                    $this->response = ['error' => 'login error'];
                    return false;
                }
                break;
            case 'gateway2':
                $paymentService = new Gateway2Service();
                break;
            default:
                $this->status = 404;
                $this->response = ['error' => 'gateway not listed'];
                return false;
        }

        if (!$paymentService->createTransaction($totalAmount, $client['name'], $client['email'], $cardNumbers, $cvv)) {
            $this->status = $paymentService->status;
            $this->response = $paymentService->response;
            return false;
        }

        $transaction = new Transaction();
        $transaction->client = $client['id'];
        $external = $paymentService->response;

        if (!$paymentService->listTransactions($external['id'])) {
            $this->status = 404;
            $this->response = ['error' => 'transaction not found'];
            return false;
        }

        $external = $paymentService->response;
        $transaction = Transaction::create([
            'client' => $client['id'],
            'gateway' => $gateway['id'],
            'external_id' => $external['id'],
            'status' => $external['status'],
            'amount' => $totalAmount,
            'card_last_numbers' => $cardLastNumber,
        ])?->toArray();

        if (!isset($transaction['id'])) {
            $this->status = 500;
            $this->response = ['error' => 'transaction create error'];
            return false;
        }

        $this->status = 200;
        $this->response = ['transaction' => $transaction];

        $transactionProductService = new TransactionProductService();

        foreach ($products as $product) {
            if (!$productService->create($product['name'], $product['amount'] * $product['quantity'])) {
                $this->status = 400;
                $this->response = ['error' => 'not possible create product', 'product' => $product];
                return false;
            }

            $newProduct = $productService->response['product']->toArray();

            if (!$transactionProductService->create($transaction['id'], $newProduct['id'], $product['quantity'])) {
                $this->status = 400;
                $this->response = ['error' => 'not possible create transaction product', 'product' => $product];
                return false;
            }
        }

        return true;
    }

    public function chargeBack(?int $id): bool
    {

        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'id not valid'];
            return false;
        }

        $transaction = Transaction::find($id);

        if (!isset($transaction->id)) {
            $this->status = 404;
            $this->response = ['error' => 'transaction not found'];
        }

        $gatewayService = new GatewayService();

        if (!$gatewayService->select($transaction->gateway)) {
            $this->status = $gatewayService->status;
            $this->response = $gatewayService->response;
            return false;
        }

        $gateway = $gatewayService->response['gateway'];

        switch ($gateway['name']) {
            case 'gateway1':
                $paymentService = new Gateway1Service();
                if (!$paymentService->login()) {
                    $this->status = 401;
                    $this->response = ['error' => 'login error'];
                    return false;
                }
                break;
            case 'gateway2':
                $paymentService = new Gateway2Service();
                break;
            default:
                $this->status = 404;
                $this->response = ['error' => 'gateway not listed'];
                return false;
        }


        if (!$paymentService->chargeBack($transaction->external_id)) {
            $this->status = $paymentService->status;
            $this->response = $paymentService->response;
            return false;
        }

        $response = $paymentService->response;

        if (!isset($response['error']) && !isset($response['status'])) {
            $transaction->status = 'charged_back';
        } elseif (!isset($response['error'])) {
            $transaction->status = $response['status'];
        } else {
            $this->status = $paymentService->status;
            $this->response = ['error' => $response['error']];
        }

        $newTransaction = $transaction;

        try {
            $newTransaction->update();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 200;
        $this->response = ['transaction' => $newTransaction->toArray()];

        return true;
    }

}