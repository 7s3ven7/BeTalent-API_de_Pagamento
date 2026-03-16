<?php

namespace App\Validators;

use App\Models\Client;

class ClientValidator extends BaseValidator
{

    public function validate(Client $client): array
    {
        if (!$this->Name($client->name)) {
            return ['response' => json_encode(['error' => "Client name isn't valid", 'name' => $client->name]), 'status' => 400];
        } elseif (!$this->Email($client->email)) {
            return ['response' => json_encode(['error' => "Client email isn't valid", 'email' => $client->email]), 'status' => 400];
        }

        return ['status' => 200];
    }

}