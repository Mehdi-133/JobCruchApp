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
        $session->set('user_role', $user['role']);
        $session->set('user_profile_image', $user['profile_image'] ?? null);
        $session->set('user_speciality', $user['speciality'] ?? null);
        $session->set('user_promo', $user['promo'] ?? null);
        $session->set('authenticated', true);
    }

    public static function logout()
    {

        $session = Session::getInstance();
        $session->remove('user_id');
        $session->remove('user_email');
        $session->remove('user_name');
        $session->remove('user_role');
        $session->remove('user_profile_image');
        $session->remove('user_speciality');
        $session->remove('user_promo');
        $session->remove('authenticated');
        $session->destroy();
        clearstatcache();
    }

    public static function check()
    {
        $session = Session::getInstance();
        $isAuthenticated = $session->get('authenticated', false);
        
        // Prevent caching of authenticated pages
        if ($isAuthenticated) {
            Session::preventCaching();
        }
        
        return $isAuthenticated;
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
            'name' => $session->get('user_name'),
            'role' => $session->get('user_role'),
            'profile_image' => $session->get('user_profile_image'),
            'speciality' => $session->get('user_speciality'),
            'promo' => $session->get('user_promo')
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

    public static function updateSession($user)
    {
        $session = Session::getInstance();
        if (isset($user['id'])) $session->set('user_id', $user['id']);
        if (isset($user['email'])) $session->set('user_email', $user['email']);
        if (isset($user['name'])) $session->set('user_name', $user['name']);
        if (isset($user['role'])) $session->set('user_role', $user['role']);
        if (isset($user['profile_image'])) $session->set('user_profile_image', $user['profile_image']);
        if (isset($user['speciality'])) $session->set('user_speciality', $user['speciality']);
        if (isset($user['promo'])) $session->set('user_promo', $user['promo']);
    }

    public static function guest()
    {
        return !self::check();
    }
}
