<?php

namespace Core;

class Auth
{
    private $id = null;
    private $name = null;
    private $email = null;

    public function __construct()
    {
        if (Session::get('user')) {
            $user = Session::get('user');
            $this->id = $user['id'];
            $this->name = $user['nome'];
            $this->email = $user['email'];
        }
    }

    public static function id()
    {
        return self::$id;
    }

    public static function name()
    {
        return self::$name;
    }
    public static function email()
    {
        return self::$email;
    }

    public function check()
    {
        if ($this->id == null || $this->name == null || $this->email == null) {
            return false;
        }
        return true;
    }
}
