<?php

namespace App\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MarkNullIfEmptyArray implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (is_array($value) && empty($value)) {
            return null;
        }

        return $value;
    }
}
