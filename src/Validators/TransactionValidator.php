<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class TransactionValidator
{

    public function cardNumber(?string $cardNumbers): bool
    {
        return v::stringType()->length(16, 16)->validate($cardNumbers);
    }

}