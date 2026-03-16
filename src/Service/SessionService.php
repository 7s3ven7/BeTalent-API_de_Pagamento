<?php

namespace App\Service;

use Exception;

class SessionService
{

    public int $status;
    public array $response;

    public function createToken(string $role): bool
    {
        try {
            $token = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $this->response = ['error' => $e->getMessage()];
            $this->status = 500;
            return false;
        }
        $this->createSession($token, $role);
        $this->status = 200;
        $this->response = ['token' => $token];
        return true;
    }

    private function createSession(string $token, string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['token'] = $token;
        $_SESSION['role'] = $role;
    }

}