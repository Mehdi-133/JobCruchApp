<?php

namespace App\core;

use App\models\User;

class Auth
{
    public static function login($user)
    {
        $session = Session::getInstance();
        $session->set('user_id', $user['id']);
        $session->set('user_email', $user['email']);
        $session->set('user_name', $user['name']);
        $session->set('authenticated', true);
    }

    public static function logout()
    {
        $session = Session::getInstance();
        $session->remove('user_id');
        $session->remove('user_email');
        $session->remove('user_name');
        $session->remove('authenticated');
    }

    public static function check()
    {
        $session = Session::getInstance();
        return $session->get('authenticated', false);
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        $session = Session::getInstance();
        return [
            'id' => $session->get('user_id'),
            'email' => $session->get('user_email'),
            'name' => $session->get('user_name')
        ];
    }

    public static function id()
    {
        $session = Session::getInstance();
        return $session->get('user_id');
    }

    public static function attempt($email, $password)
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!isset($user['password'])) {
            return false;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }

        self::login($user);
        return true;
    }

    public static function guest()
    {
        return !self::check();
    }
}
