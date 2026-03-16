<?php

namespace App\Validators;

use App\Models\Product;

class ProductValidator extends BaseValidator
{

    public int $status;
    public array $response;

    public function validate(Product $product): bool
    {
        if (!$this->Name($product->name)) {
            $this->status = 400;
            $this->response = ['error' => 'Name invalid'];
            return false;
        } elseif (!$this->Amount($product->amount)) {
            $this->status = 400;
            $this->response = ['error' => 'Amount invalid'];
            return false;
        }

        $this->status = 200;
        $this->response = [];
        return true;
    }

}