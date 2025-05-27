<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\Helpers\RbacHelper;
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

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_SETTINGS_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::telegramBotSettings())->orderBy('id')->get();
    }
}
