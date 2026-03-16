<?php

namespace App\Service;

use App\Models\TransactionProduct;

class TransactionProductService
{

    public int $status;
    public array $response;

    public function create(?int $transaction_id, ?int $product_id, ?int $quantity): bool
    {
        if (!is_int($transaction_id) || !is_int($product_id) || !is_int($quantity)) {
            $this->status = 400;
            $this->response = ['error' => 'Data invalid', 'transaction_id' => $transaction_id, 'product_id' => $product_id, 'quantity' => $quantity];
            return false;
        }

        $transactionProduct = TransactionProduct::create([
            'transaction_id' => $transaction_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ])?->toArray();

        if (!isset($transactionProduct['id'])) {
            $this->status = 400;
            $this->response = ['error' => 'not possible create transaction product', 'transaction' => $transactionProduct];
            return false;
        }

        $this->status = 200;
        $this->response = ['transaction' => $transactionProduct];
        return true;
    }

}