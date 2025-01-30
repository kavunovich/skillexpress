<?php
class TokenDetector {
    public const REGISTER = '/register/';
    public const AUTH = '/auth/';
    public const PROFILE = '/profile/';
    public const MAIN = '/';
    public string $protocol = 'http://';
    public string $domain;

    public function __construct() {
        $this->domain = $_SERVER['HTTP_HOST'];
        
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
          $this->protocol = 'https://';
        }
    }

    public function exist(): bool {
        return isset($_COOKIE['token']);
    }

    public function goto(string $id): void {
        header("Location: {$this->protocol}{$this->domain}$id");
    }

    public function getToken(): mixed {
        return $_COOKIE['token'] ?? '';
    }
}