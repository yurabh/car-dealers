<?php

namespace App\Http\Requests;

/**
 * PUT  = full replacement: all fields are required (same rules as on create).
 * PATCH = partial update: every field may be omitted, but is validated when present.
 */
trait MakesRulesOptional
{
    public function rules(): array
    {
        $rules = parent::rules();

        if (!$this->isMethod('PATCH')) {
            return $rules;
        }

        return array_map(
            fn(array $fieldRules) => ['sometimes', ...$fieldRules],
            $rules
        );
    }
}
