<?php

declare(strict_types=1);

namespace App\GraphQL\Scalars;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

final class BigFloat extends ScalarType implements TypeConvertible
{
    public const string GRAPHQL_TYPE_NAME = 'BigFloat';

    public string $name = self::GRAPHQL_TYPE_NAME;

    public ?string $description = 'The `BigFloat` scalar type represents non-fractional signed whole numeric values.';

    /**
     * Serializes an internal value to include in a response.
     *
     * @param  mixed  $value
     *
     * @throws Error
     */
    public function serialize($value): int|float
    {
        if (! preg_match('/[+-]?([0-9]*[.])?[0-9]+/', (string) $value)) {
            throw new Error($value . ' cannot represent a big float.');
        }

        return strpos((string) $value, '.') === false ? (int) $value : (float) $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param  mixed  $value
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        if (! preg_match('/\d+/', (string) $value)) {
            throw new Error($value . ' cannot represent a big float.');
        }

        return (string) $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param  Node  $valueNode
     * @param  mixed[]|null  $variables
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null): mixed
    {
        if (! $valueNode instanceof StringValueNode) {
            throw new Error(
                "Query error: Can only parse strings got: {$valueNode->kind}",
                $valueNode
            );
        }

        if (! preg_match('/\d+/', (string) ($valueNode->value))) {
            throw new Error($valueNode->value . ' cannot represent a big float.');
        }

        return $valueNode->value;
    }

    public function toType(): Type
    {
        return new self;
    }
}
