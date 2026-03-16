<?php

namespace App\Validators;

use App\Models\User;

class UserValidator extends BaseValidator
{

    public int $status;
    public array $response;

    public function validate(User $user): bool
    {
        $validate = new BaseValidator();

        if (!$validate->Email($user->email)) {
            $this->status = 409;
            $this->response = ['error' => 'email not valid', 'email' => $user->email];
            return false;
        } elseif (!$validate->Password($user->password)) {
            $this->status = 400;
            $this->response = ['error' => 'password not valid', 'password' => $user->password];
            return false;
        } elseif (!$validate->Role($user->role)) {
            $this->status = 400;
            $this->response = ['error' => 'role not valid', 'role' => $user->role];
            return false;
        }

        $user->password = password_hash($user->password, PASSWORD_DEFAULT);

        $this->status = 200;
        $this->response = [];
        return true;
    }

}