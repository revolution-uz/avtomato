<?php

namespace App\GraphQL\Customer;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Mutations\AttachAddressGraphQLMutation;
use App\GraphQL\Customer\Mutations\AttachCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\AttachPhoneGraphQLMutation;
use App\GraphQL\Customer\Mutations\ConfirmCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\CreateCustomerGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachAddressGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachPhoneGraphQLMutation;
use App\GraphQL\Customer\Mutations\UpdateCustomerGraphQLMutation;
use App\GraphQL\Customer\Queries\CustomerByPassportGraphQLQuery;
use App\GraphQL\Customer\Queries\CustomersGraphQLQuery;
use App\GraphQL\Customer\Queries\GenerateCustomerApplicationDocumentGraphQLQuery;
use App\GraphQL\Customer\Queries\InfoByPassportGraphQLQuery;
use App\GraphQL\Customer\Queries\SingleCustomerGraphQLQuery;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\AddressInputGraphQLType;
use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\GraphQL\Customer\Types\AttachCardGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerAddressByPassportDataGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Customer\Types\PassportDataGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Customer\Types\PhoneInputGraphQLType;
use App\GraphQL\Installment\Types\InstallmentCommentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\GraphQL\Installment\Types\TariffGraphQLType;
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
use App\GraphQL\Scoring\Types\Card\CardCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardMonthlyAnalyticsGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardMonthlyDetailedAnalyticsGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringResponseGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\CreditHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryAdditionalInfoGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryDebtGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryScoreGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomeGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomesGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomesPeriodGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmInpsHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmPresenceReportGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmPresenceReportsGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmClientGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionDataGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionInfoResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionRowGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionSizePensionGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\PensionInfoGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestUnionGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CustomerQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CustomersGraphQLQuery::class,
                SingleCustomerGraphQLQuery::class,
                InfoByPassportGraphQLQuery::class,
                CustomerByPassportGraphQLQuery::class,
                GenerateCustomerApplicationDocumentGraphQLQuery::class,
            ],
            'mutation' => [
                AttachAddressGraphQLMutation::class,
                AttachCardGraphQLMutation::class,
                AttachPhoneGraphQLMutation::class,
                ConfirmCardGraphQLMutation::class,
                CreateCustomerGraphQLMutation::class,
                DetachAddressGraphQLMutation::class,
                DetachCardGraphQLMutation::class,
                DetachPhoneGraphQLMutation::class,
                UpdateCustomerGraphQLMutation::class,
            ],
            'types' => [
                AddressGraphQLType::class,
                AddressInputGraphQLType::class,
                AttachCardDataGraphQLType::class,
                AttachCardGraphQLType::class,
                CardGraphQLType::class,
                CustomerGraphQLType::class,
                CustomerInputGraphQLType::class,
                PhoneGraphQLType::class,
                PhoneInputGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                PassportDataGraphQLType::class,
                CustomerAddressByPassportDataGraphQLType::class,

                AttachmentGraphQLType::class,
                AdminGraphQLType::class,
                TemplateThemeGraphQLType::class,
                RoleGraphQLType::class,
                PermissionGroupGraphQLType::class,
                PermissionGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionBranchItemGraphQLType::class,

                // installment types
                InstallmentGraphQLType::class,
                InstallmentGraphGraphQLType::class,
                TransactionGraphQLType::class,
                InstallmentGuarantorGraphQLType::class,
                InstallmentCommentGraphQLType::class,
                BranchGraphQLType::class,
                TariffGraphQLType::class,
                TransactionTypeGraphQLType::class,

                //scoring
                CardCreateInputGraphQLType::class,
                CardMonthlyAnalyticsGraphQLType::class,
                CardMonthlyDetailedAnalyticsGraphQLType::class,
                CardScoringGraphQLType::class,
                CardScoringRequestGraphQLType::class,
                CardScoringResponseGraphQLType::class,

                DebtCreateInputGraphQLType::class,
                DebtScoringGraphQLType::class,
                DebtScoringRequestGraphQLType::class,
                DebtScoringResponseGraphQLType::class,

                CreditHistoryGraphQLType::class,
                KatmCreditHistoryAdditionalInfoGraphQLType::class,
                KatmCreditHistoryDebtGraphQLType::class,
                KatmCreditHistoryResponseGraphQLType::class,
                KatmCreditHistoryScoreGraphQLType::class,

                KatmClientGraphQLType::class,
                KatmCreateInputGraphQLType::class,
                KatmCreateRequestGraphQLType::class,

                InpsHistoryGraphQLType::class,
                KatmIncomeGraphQLType::class,
                KatmIncomesGraphQLType::class,
                KatmIncomesPeriodGraphQLType::class,
                KatmInpsHistoryResponseGraphQLType::class,
                KatmPresenceReportGraphQLType::class,
                KatmPresenceReportsGraphQLType::class,

                KatmPensionDataGraphQLType::class,
                KatmPensionInfoResponseGraphQLType::class,
                KatmPensionRowGraphQLType::class,
                KatmPensionSizePensionGraphQLType::class,
                PensionInfoGraphQLType::class,

                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,

                // unions last
                ScoringRequestUnionGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
