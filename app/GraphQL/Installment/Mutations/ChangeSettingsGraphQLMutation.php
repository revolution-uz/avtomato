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
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ChangeSettingsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeSettings',
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

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SETTINGS_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ($args['settings_id'] === CompanySettings::query()->firstWhere('name', CompanySettingsEnum::SMS_INSTALLMENT_AUTO_CHARGE)->getKey()) {
            throw ValidationException::withMessages(['settings_id' => 'Cannot edit SMS installment auto charge setting.']);
        }

        CompanySettings::query()->where('id', $args['settings_id'])->update(['value' => $args['value']]);

        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::installmentSettings())->orderBy('id')->get();
    }
}
