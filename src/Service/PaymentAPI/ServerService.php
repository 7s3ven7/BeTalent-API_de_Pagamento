<?php

namespace App\Service\PaymentAPI;

use CurlHandle;

class ServerService
{

    public string $url;
    public int $status;
    public array $response;

    public function init(string $endpoint): CurlHandle|bool
    {
        return curl_init($this->url . $endpoint);
    }

    public function get(string $endpoint, array $header = []): bool
    {
        $api = $this->init($endpoint);

        if (!$api) {
            return false;
        }

        curl_setopt($api, CURLOPT_HTTPGET, true);
        curl_setopt($api, CURLOPT_HTTPHEADER, $header);
        curl_setopt($api, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($api);

        if ($response === false) {
            $this->status = 401;
            $this->response = ['error' => 'not possible to send request, gateway1'];
            return false;
        }

        $this->response = json_decode($response, true);

        if (isset($this->response['error'])) {
            $this->status = 401;
            $this->response = ['error' => $this->response['error']];
            return false;
        }

        $this->status = 200;

        return true;
    }

    public function post(string $endpoint, array $params, array $header = []): bool
    {
        $api = $this->init($endpoint);

        if (!$api) {
            return false;
        }

        curl_setopt($api, CURLOPT_POST, true);
        curl_setopt($api, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($api, CURLOPT_HTTPHEADER, $header);
        curl_setopt($api, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($api);
        if ($response === false) {
            $this->status = 401;
            $this->response = ['error' => 'not possible to send request, gateway1'];
            return false;
        }

        $this->response = json_decode($response, true);

        if (isset($this->response['error'])) {
            $this->status = 401;
            $this->response = ['error' => $this->response['error']];
            return false;
        }

        $this->status = 200;

        return true;
    }

}
