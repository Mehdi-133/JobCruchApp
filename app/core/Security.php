<?php

namespace App\core;

class Security
{
    public static function generateToken()
    {
        $session = Session::getInstance();
        $token = bin2hex(random_bytes(32));
        $session->set('csrf_token', $token);
        return $token;
    }

    public static function getToken()
    {
        $session = Session::getInstance();
        if (!$session->has('csrf_token')) {
            return self::generateToken();
        }
        return $session->get('csrf_token');
    }

    public static function validateToken($token)
    {
        $session = Session::getInstance();
        $storedToken = $session->get('csrf_token');

        if (!$storedToken || !$token) {
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    public static function regenerateToken()
    {
        $session = Session::getInstance();
        $session->remove('csrf_token');
        return self::generateToken();
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword($password, $hash)
    {
        if (!$hash) {
            return false;
        }
        return password_verify($password, $hash);
    }

    public static function escape($data)
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitize($data)
    {
        return trim(strip_tags($data));
    }

    public static function generateRandomString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}
