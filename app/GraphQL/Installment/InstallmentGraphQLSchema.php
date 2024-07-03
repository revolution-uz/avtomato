<?php

namespace App\GraphQL\Installment;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Installment\Mutations\AttachCardGraphQLMutation;
use App\GraphQL\Installment\Mutations\AttachGuarantorGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateInstallmentGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteInstallmentGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\DetachCardGraphQLMutation;
use App\GraphQL\Installment\Mutations\DetachGuarantorGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateInstallmentGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Queries\CalculateGraphQLQuery;
use App\GraphQL\Installment\Queries\InstallmentsGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleInstallmentGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionTypeGraphQLQuery;
use App\GraphQL\Installment\Queries\TariffsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionTypesGraphQLQuery;
use App\GraphQL\Installment\Types\CalculatedGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentCommentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\GraphQL\Installment\Types\InstallmentInputGraphQLType;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\GraphQL\Installment\Types\TariffInputGraphQLType;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\GraphQL\Management\Types\PermissionBranchItemGraphQLType;
use App\GraphQL\Management\Types\PermissionEditableItemGraphQLType;
use App\GraphQL\Management\Types\PermissionGraphQLType;
use App\GraphQL\Management\Types\PermissionGroupGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\GraphQL\Management\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class InstallmentGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CalculateGraphQLQuery::class,
                InstallmentsGraphQLQuery::class,
                SingleInstallmentGraphQLQuery::class,
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                TransactionTypesGraphQLQuery::class,
                SingleTransactionTypeGraphQLQuery::class,
                TransactionsGraphQLQuery::class,
                SingleTransactionGraphQLQuery::class,
            ],
            'mutation' => [
                CreateInstallmentGraphQLMutation::class,
                UpdateInstallmentGraphQLMutation::class,
                DeleteInstallmentGraphQLMutation::class,
                CreateTariffGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                DeleteTariffGraphQLMutation::class,
                CreateTransactionTypeGraphQLMutation::class,
                UpdateTransactionTypeGraphQLMutation::class,
                DeleteTransactionTypeGraphQLMutation::class,
                CreateInstallmentCommentGraphQLMutation::class,
                UpdateInstallmentCommentGraphQLMutation::class,
                DeleteInstallmentCommentGraphQLMutation::class,
                AttachCardGraphQLMutation::class,
                DetachCardGraphQLMutation::class,
                AttachGuarantorGraphQLMutation::class,
                DetachGuarantorGraphQLMutation::class,
            ],
            'types' => [
                InstallmentInputGraphQLType::class,
                CalculatedGraphGraphQLType::class,
                AdminGraphQLType::class,
                TemplateThemeGraphQLType::class,
                RoleGraphQLType::class,
                PermissionGroupGraphQLType::class,
                PermissionGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionBranchItemGraphQLType::class,
                AddressGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                PhoneGraphQLType::class,
                BranchGraphQLType::class,
                InstallmentGraphQLType::class,
                InstallmentGraphGraphQLType::class,
                TariffInputGraphQLType::class,
                TariffGraphQLType::class,
                TransactionTypeGraphQLType::class,
                CustomerGraphQLType::class,
                CardGraphQLType::class,
                AttachmentGraphQLType::class,
                TransactionGraphQLType::class,
                InstallmentCommentGraphQLType::class,
                InstallmentGuarantorGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
