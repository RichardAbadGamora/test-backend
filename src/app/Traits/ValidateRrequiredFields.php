<?php

namespace App\Traits;

trait ValidateRrequiredFields
{
    public function validateRequiredFields(array $data, array $requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("'$field' field is required.");
            }
        }
    }
}
