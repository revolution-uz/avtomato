<?php

namespace App\GraphQL\Base;

use GraphQL\Type\Definition\Type as GraphQLType;
use Rebing\GraphQL\Support\PaginationType;

class GraphQLPaginationType extends PaginationType
{
    protected function getPaginationFields(GraphQLType $underlyingType): array
    {
        $parentFields = parent::getPaginationFields($underlyingType);
        if (property_exists($underlyingType, 'config') && array_key_exists('model', $underlyingType->config) && $underlyingType->config['model'] && ($underlyingType->config['model']::$sortables ?? null)) {
            $sortables = $underlyingType->config['model']::$sortables;
            $parentFields['sortables'] = [
                'type' => GraphQLType::nonNull(GraphQLType::listOf(GraphQLType::string())),
                'description' => 'Sortable columns of data',
                'resolve' => fn () => $sortables,
            ];
        }

        return $parentFields;
    }
}
