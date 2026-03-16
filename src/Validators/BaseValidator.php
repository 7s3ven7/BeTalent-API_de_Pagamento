<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class BaseValidator
{

    public function Json(mixed $data): bool
    {
        return v::arrayType()->validate($data);
    }

    public function Email(mixed $data): bool
    {
        return v::email()->length(11, 255)->validate($data);
    }

    public function Password(mixed $data): bool
    {
        return v::stringType()->length(6, 255)->validate($data);
    }

    public function Role(mixed $role): bool
    {
        $allRoles = ['ADMIN', 'MANAGER', 'FINANCE', 'USER'];

        if (in_array($role, $allRoles, true)) {
            return true;
        }

        return false;
    }

    public function Amount(mixed $amount): bool
    {
        return v::intType()->length(1, 11)->validate($amount);
    }

    public function Name(mixed $name): bool
    {
        return v::stringType()->length(1, 255)->validate($name);
    }

    public function int(mixed $int): bool
    {
        return v::intVal()->validate($int);
    }

    public function boolInt(mixed $bool): bool
    {
        return v::intVal()->between(0, 1)->validate($bool);
    }

}