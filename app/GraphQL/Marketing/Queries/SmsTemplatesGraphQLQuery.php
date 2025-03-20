<?php

namespace App\GraphQL\Marketing\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\SmsTemplateEnum;
use App\Enums\FeatureEnum;
use App\GraphQL\Marketing\Types\SmsTemplateGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Feature;
use App\Models\Company\SmsTemplate;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Query;

class SmsTemplatesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'smsTemplates',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(SmsTemplateGraphQLType::type())));
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SMS_TEMPLATE_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return SmsTemplate::query()
            ->when(! Feature::isFeatureActive(FeatureEnum::ECOMMERCE), fn (Builder|SmsTemplate $q) => $q
                ->whereNotIn('id', [SmsTemplateEnum::ECOMMERCE_OTP->value]))
            ->when(isset($args['search']), fn (Builder|SmsTemplate $q) => $q
                ->where('template', 'ILIKE', "%{$args['search']}%")
                ->orWhere('original_template', 'ILIKE', "%{$args['search']}%"))
            ->when(
                isset($args['sort']),
                fn (Builder|SmsTemplate $q) => $q->orderBySortable($args['sort']),
                fn (Builder|SmsTemplate $q) => $q->orderByDesc('id')
            )
            ->get();
    }
}
