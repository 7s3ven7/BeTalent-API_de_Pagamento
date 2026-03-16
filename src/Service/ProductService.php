<?php

namespace App\Service;

use App\Models\Product;
use App\Validators\ProductValidator;
use Exception;

class ProductService
{

    public int $status;
    public array $response;

    public function read(?int $id): bool
    {
        $validate = new ProductValidator();

        if ($id !== null) {

            $product = Product::find($id)?->toArray();

            if (!$validate->Json($product)) {
                $this->status = 404;
                $this->response = ['error' => 'Product not found', 'id' => $id];
                return false;
            }

            $this->status = 200;
            $this->response = ['product' => $product];
            return true;
        }

        $product = Product::all()?->toArray();

        $this->status = 200;
        $this->response = ['products' => $product];
        return true;
    }

    public function create(?string $name, ?int $amount): bool
    {
        $product = new Product();
        $product->name = $name;
        $product->amount = $amount;

        $validate = new ProductValidator();
        $validate->validate($product);

        if (!$validate->validate($product)) {
            $this->status = $validate->status;
            $this->response = $validate->response;
            return false;
        }

        try {
            $product->save();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }
        $this->status = 201;
        $this->response = ['product' => $product];
        return true;
    }

    public function update(?int $id, ?string $name, ?int $amount): bool
    {
        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id invalid', 'id' => $id];
            return false;
        }

        $product = Product::find($id);

        if (!isset($product->id)) {
            $this->status = 404;
            $this->response = ['error' => 'Product not found', 'id' => $id];
            return false;
        }

        $validate = new ProductValidator();
        $productUpdated = new Product();
        $productUpdated->name = $name;
        $productUpdated->amount = $amount;

        if ($validate->validate($productUpdated)) {
            $this->status = $validate->status;
            $this->response = $validate->response;
            return false;
        }

        try {
            $product->update($productUpdated->toArray());
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 200;
        $this->response = ['product' => $product];
        return true;
    }

    public function delete(?int $id): bool
    {

        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id invalid', 'id' => $id];
            return false;
        }

        $product = Product::find($id);

        if (!isset($product->id)) {
            $this->status = 404;
            $this->response = ['error' => 'Product not found', 'id' => $id];
            return false;
        }

        try {
            $product->delete();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 204;
        $this->response = [];
        return true;
    }

}