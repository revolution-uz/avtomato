<?php

namespace App\GraphQL\Common\Types;

use App\Enums\ModuleEnum;
use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Module;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ModuleGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'ModuleGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        $fields = [];

        foreach (ModuleEnum::cases() as $case) {
            $fields[$case->value] = [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => function (Collection $root) use ($case) {
                    /** @var Collection<int, Module> $root */
                    return $root->first(fn (Module $module) => $module->name === $case)->is_active ?? false;
                },
            ];
        }

        return $fields;
    }
}
