<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class UpdateTelegramBotSettingsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTelegramBotSettings',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(CompanySettings::class, 'id')->whereIn('name', CompanySettingsEnum::telegramBotSettings()),
                ],
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(SettingsGraphQLType::type());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SETTINGS_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $companySettings = CompanySettings::query()->find($args['id']);
        $companySettings->update(['value' => $args['value']]);

        return $companySettings;
    }
}
