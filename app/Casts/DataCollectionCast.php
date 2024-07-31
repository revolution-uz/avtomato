<?php

namespace App\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class DataCollectionCast implements Cast
{
    public function __construct(
        public string $class
    ) {}

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?DataCollection
    {
        if (! array_is_list($value)) {
            return null;
        }

        return new DataCollection($this->class, $value);
    }
}
