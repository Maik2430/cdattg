<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns\Complementarios\CreateAspirante;

trait HandlesCreateAspirantePrepareForValidationHelpers
{
    /**
     * @param  array<int, string>  $fields
     */
    private function trimFields(array $fields, bool $skipNull = false): void
    {
        foreach ($fields as $field) {
            $this->trimField($field, $skipNull);
        }
    }

    private function trimField(string $field, bool $skipNull = false, ?callable $transform = null): void
    {
        if (! $this->has($field)) {
            return;
        }

        $value = $this->{$field};

        if ($value === null && $skipNull) {
            return;
        }

        $formatted = $transform ? $transform((string) $value) : trim((string) $value);

        $this->merge([$field => $formatted]);
    }
}
