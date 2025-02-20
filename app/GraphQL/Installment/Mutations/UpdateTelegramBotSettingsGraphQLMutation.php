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
use Rebing\GraphQL\Support\Mutation;

class UpdateTelegramBotSettingsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTelegramBotSettings',
    ];

    public function args(): array
    {
        return [
            'bot_enabled' => [
                'name' => 'bot_enabled',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'bot_token' => [
                'name' => 'bot_token',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'chat_id' => [
                'name' => 'chat_id',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(SettingsGraphQLType::type()));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SETTINGS_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['bot_enabled'])) {
            CompanySettings::query()
                ->where('name', CompanySettingsEnum::TELEGRAM_BOT_ENABLED)
                ->update(['value' => $args['bot_enabled']]);
        }

        if (isset($args['bot_token'])) {
            CompanySettings::query()
                ->where('name', CompanySettingsEnum::TELEGRAM_BOT_TOKEN)
                ->update(['value' => $args['bot_token']]);
        }

        if (isset($args['chat_id'])) {
            CompanySettings::query()
                ->where('name', CompanySettingsEnum::TELEGRAM_CHAT_ID)
                ->update(['value' => $args['chat_id']]);
        }

        return CompanySettings::query()->whereIn('name', CompanySettingsEnum::telegramBotSettings())->orderBy('id')->get();
    }
}
