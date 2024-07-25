<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\CompanySettingsEnum;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\Models\Company\CompanySettings;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ChangeInstallmentSettingsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeInstallmentSettings',
    ];

    public function args(): array
    {
        return [
            'settings_id' => [
                'name' => 'settings_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists('settings', 'id')->whereIn('name', CompanySettingsEnum::installmentSettings()),
                ],
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(SettingsGraphQLType::GRAPHQL_TYPE_NAME))));

    }

    // public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    // {
    //     return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_CARD);
    // }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        CompanySettings::query()->where('id', $args['settings_id'])->update(['value' => $args['value']]);

        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::installmentSettings())->get();
    }
}