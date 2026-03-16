<?php

namespace App\Service;

use App\Models\Client;
use App\Validators\ClientValidator;
use Exception;

class ClientService
{

    public int $status;
    public array $response;

    public function read(?int $id = null): bool
    {

        $validator = new ClientValidator();

        if (is_int($id)) {
            $client = Client::find($id)?->toArray();

            if (!$validator->Json($client)) {
                $this->status = 400;
                $this->response = ['error' => 'client not found', 'id' => $id];
                return false;
            }

            $this->status = 200;
            $this->response = ['client' => $client];
            return true;
        }

        $client = Client::all()?->toArray();

        $this->status = 200;
        $this->response = ['clients' => $client];
        return true;
    }

    public function create(?string $name, ?string $email): bool
    {
        $client = new Client();
        $client->name = $name;
        $client->email = $email;

        $clientValidate = new ClientValidator();

        if (!$clientValidate->validate($client)) {
            $this->status = 400;
            $this->response = ['error' => 'client data invalid', 'client' => $client];
            return false;
        }
        if (Client::all()->where('email', $email)->count() > 0) {
            $this->status = 409;
            $this->response = ['error' => 'Client already exists', 'email' => $email];
            return false;
        }

        try {
            $client->save();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage()];
            return false;
        }

        $this->status = 200;
        $this->response = ['client' => $client];
        return true;
    }

}