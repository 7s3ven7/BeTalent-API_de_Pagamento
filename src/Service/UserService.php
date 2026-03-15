<?php

namespace App\Service;

use App\Models\User;
use App\Validators\UserValidator;
use Exception;

class UserService
{

    public int $status;
    public array $response;

    public function login(?string $email, ?string $password): bool
    {
        $validator = new UserValidator();

        if (!$validator->Email($email) || !$validator->Password($password)) {
            $this->status = 400;
            $this->response = ['error' => 'Data Invalid', 'email' => $email, 'password' => $password];
            return false;
        }

        $user = User::where('email', $email)->first()?->toArray();

        if (!$validator->Json($user)) {
            $this->status = 404;
            $this->response = ['error' => 'User not found', 'user' => $user];
            return false;
        } elseif (!password_verify($password, $user['password'])) {
            $this->status = 400;
            $this->response = ['error' => 'Password invalid', 'password' => $password];
            return false;
        }

        $session = new SessionService();
        $token = $session->createToken($user['role']);

        if (!$token) {
            $this->status = $session->status;
            $this->response = $session->response;
            return false;
        }

        $this->status = 200;
        $this->response = $session->response;
        return true;
    }

    public function read(?int $id): bool
    {
        $validate = new UserValidator();

        if (is_int($id)) {
            $user = User::find($id)?->toArray();

            if (!$validate->Json($user)) {
                $this->status = 400;
                $this->response = ['error' => 'user not found', 'id' => $id];
                return false;
            }

            $this->status = 200;
            $this->response = ['user' => $user];
            return true;
        }

        $user = User::all()?->toArray();

        $this->status = 200;
        $this->response = ['users' => $user];
        return true;
    }

    public function create(?string $email, ?string $password, ?string $role): bool
    {
        $user = new User();
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;

        $validate = new UserValidator();

        if (!$validate->validate($user)) {
            $this->status = $validate->status;
            $this->response = $validate->response;
            return false;
        }

        if (User::all()->where('email', $email)->count() > 0) {
            $this->status = 409;
            $this->response = ['error' => 'User already exists', 'email' => $email];
            return false;
        }

        try {
            $user->save();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage(), 'user' => $user];
            return false;
        }
        $this->status = 200;
        $this->response = ['user' => $user->attributesToArray()];
        return true;
    }

    public function update(?int $id, ?string $email, ?string $password, ?string $role): bool
    {
        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id invalid', 'id' => $id];
            return false;
        }

        $user = User::find($id);

        if (!isset($user->id)) {
            $this->status = 404;
            $this->response = ['error' => 'User not found', 'id' => $id];
            return false;
        }

        $userUpdate = new User();
        $userUpdate->email = $email;
        $userUpdate->password = $password;
        $userUpdate->role = $role;

        $validate = new UserValidator();


        if (!$validate->validate($userUpdate)) {
            $this->status = $validate->status;
            $this->response = $validate->response;
            return false;
        }

        try {
            $user->update($userUpdate->toArray());
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage(), 'user' => $userUpdate];
            return false;
        }

        $this->status = 200;
        $this->response = ['user' => $user];
        return true;
    }

    public function delete(?int $id): bool
    {
        if (!is_int($id)) {
            $this->status = 400;
            $this->response = ['error' => 'Id invalid', 'id' => $id];
            return false;
        }

        $user = User::find($id);

        if (!isset($user->id)) {
            $this->status = 404;
            $this->response = ['error' => 'User not found', 'id' => $id];
            return false;
        }

        try {
            $user->delete();
        } catch (Exception $e) {
            $this->status = 500;
            $this->response = ['error' => $e->getMessage(), 'user' => $user];
            return false;
        }
        $this->status = 204;
        $this->response = [];
        return true;
    }

}