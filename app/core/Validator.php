<?php

namespace App\core;

class Validator
{
    private $errors = [];
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function required($field, $message = null)
    {
        if (empty($this->data[$field])) {
            $this->errors[$field][] = $message ?? "$field is required";
        }
        return $this;
    }

    public function email($field, $message = null)
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message ?? "$field must be a valid email";
        }
        return $this;
    }


    public function min($field, $length, $message = null)
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field][] = $message ?? "$field must be at least $length characters";
        }
        return $this;
    }

    public function max($field, $length, $message = null)
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field][] = $message ?? "$field must not exceed $length characters";
        }
        return $this;
    }


    public function passes()
    {
        return empty($this->errors);
    }

    public function fails()
    {
        return !$this->passes();
    }

    public function errors()
    {
        return $this->errors;
    }

    public function getError($field)
    {
        return $this->errors[$field][0] ?? null;
    }
}
