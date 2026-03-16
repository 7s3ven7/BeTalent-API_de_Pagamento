<?php

namespace App\Validators;

class GatewayValidator extends BaseValidator
{

    public function priority(mixed $priority): bool
    {
        return $this->int($priority);
    }

    public function is_active(mixed $is_active): bool
    {
        return $this->boolInt($is_active);
    }

}