<?php
// app/Core/Validator.php
namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' is required.';
                } elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = 'Invalid email address.';
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) $this->errors[$field][] = "Must be at least {$min} characters.";
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) $this->errors[$field][] = "Must be no more than {$max} characters.";
                } elseif ($rule === 'numeric' && !is_numeric($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a number.';
                } elseif ($rule === 'age_18' && !empty($value)) {
                    $dob = new \DateTime($value);
                    $now = new \DateTime();
                    if ($dob->diff($now)->y < 18) $this->errors[$field][] = 'You must be 18 or older.';
                }
            }
        }
        return empty($this->errors);
    }

    public function getErrors(): array { return $this->errors; }
    public function firstError(string $field): string { return $this->errors[$field][0] ?? ''; }
}