<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class DifferentIfNotNull implements ValidationRule, DataAwareRule
{
    protected string $otherField;
    protected array $data = []; // Holds all validation data

    /**
     * Create a new rule instance.
     *
     * @param  string  $otherField
     */
    public function __construct(string $otherField)
    {
        $this->otherField = $otherField;
    }

    /**
     * Set all the data under validation.
     *
     * @param  array<string, mixed>  $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $otherValue = $this->data[$this->otherField] ?? null;

        // Allow validation to pass if either field is null
        if (is_null($value) || is_null($otherValue)) {
            return;
        }

        // If both fields have values, ensure they are different
        if ($value === $otherValue) {
            $fail("The {$attribute} must be different from {$this->otherField} if both fields have values.");
        }
    }
}
