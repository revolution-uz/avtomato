<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class TelegramBotSettingsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'telegramBotSettings',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(SettingsGraphQLType::type()));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::telegramBotSettings())->orderBy('id')->get();
    }
}
