<?php

namespace App\core;

class Validator
{
    private $errors = [];
    private $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Validate data with rules
     * @param array $rules - Format: ['field' => 'required|min:3|max:50']
     */
    public function validate($rules)
    {
        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        return $this;
    }

    /**
     * Apply a single rule to a field
     */
    private function applyRule($field, $rule)
    {
        // Parse rule and parameters
        if (strpos($rule, ':') !== false) {
            list($ruleName, $parameter) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        // Apply the rule
        switch ($ruleName) {
            case 'required':
                $this->required($field);
                break;
            case 'email':
                $this->email($field);
                break;
            case 'min':
                $this->min($field, (int)$parameter);
                break;
            case 'max':
                $this->max($field, (int)$parameter);
                break;
            case 'same':
                $this->same($field, $parameter);
                break;
        }
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

    /**
     * Validate that field matches another field
     */
    public function same($field, $otherField, $message = null)
    {
        if (isset($this->data[$field]) && isset($this->data[$otherField])) {
            if ($this->data[$field] !== $this->data[$otherField]) {
                $this->errors[$field][] = $message ?? "$field must match $otherField";
            }
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
