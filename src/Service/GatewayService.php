<?php

namespace App\Service;

use App\Models\Gateway;
use App\Validators\GatewayValidator;
use Exception;

class GatewayService
{

    public int $status;
    public array $response;

    public function select(?int $id): bool
    {
        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'id not valid', 'id' => $id];
            return false;
        }

        $gateway = Gateway::find($id);

        if (!isset($gateway->id)) {
            $this->status = 400;
            $this->response = ['error' => 'id not valid', 'id' => $id];
            return false;
        }

        $this->status = 200;
        $this->response = ['gateway' => $gateway];
        return true;
    }

    public function getByPriority(): bool
    {

        $gateway = Gateway::all()->where('is_active', '=', 1)->sortByDesc('priority')->first()?->toArray();

        if ($gateway === null) {
            $this->status = 404;
            $this->response = ['error' => 'nothing gateway active'];
            return false;
        }

        $this->status = 200;
        $this->response = ['gateway' => $gateway];
        return true;

    }

    public function updatePriority(?int $id, ?int $priority): bool
    {
        if (!$this->select($id)) {
            return false;
        }

        $gateway = $this->response['gateway'];
        $gatewayUpdate = new Gateway();
        $gatewayValidate = new GatewayValidator();

        if (!$gatewayValidate->priority($priority)) {
            $this->status = 400;
            $this->response = ['error' => 'priority not accepted', 'priority' => $priority];
            return false;
        }

        $gateway->priority = $priority;

        try {
            $gateway->update($gatewayUpdate->toArray());
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 200;
        $this->response = ['gateway' => $gateway];
        return true;
    }

    public function updateActive(?int $id, ?int $active): bool
    {

        if (!$this->select($id)) {
            return false;
        }

        $gateway = $this->response['gateway'];
        $gatewayUpdate = new Gateway();
        $gatewayValidate = new GatewayValidator();

        if (!$gatewayValidate->is_active($active)) {
            $this->status = 400;
            $this->response = ['error' => 'active not accepted', 'active' => $active];
            return false;
        }

        $gateway->is_active = $active;

        try {
            $gateway->update($gatewayUpdate->toArray());
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 200;
        $this->response = ['gateway' => $gateway];
        return true;
    }

}