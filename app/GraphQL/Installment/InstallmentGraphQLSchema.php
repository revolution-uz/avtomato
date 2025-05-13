<?php

namespace App\GraphQL\Installment;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Mutations\UpdateCustomerPinflCardsGraphQLMutation;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Customer\Types\TagGraphQLType;
use App\GraphQL\Customer\Types\UniredCardGraphQLType;
use App\GraphQL\Installment\Mutations\AttachCardGraphQLMutation;
use App\GraphQL\Installment\Mutations\AttachGuarantorGraphQLMutation;
use App\GraphQL\Installment\Mutations\ChangeSettingsGraphQLMutation;
use App\GraphQL\Installment\Mutations\ChangeStatusGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateDocumentGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateInstallmentGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTransactionGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteDocumentGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteTransactionGraphQLMutation;
use App\GraphQL\Installment\Mutations\DeleteTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\DetachCardGraphQLMutation;
use App\GraphQL\Installment\Mutations\DetachGuarantorGraphQLMutation;
use App\GraphQL\Installment\Mutations\ReverseTransactionGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateGuarantorGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateInstallmentCommentGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateInstallmentCustomerAnyCardChargeGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateInstallmentGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTelegramBotSettingsGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTransactionGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Queries\CalculateGraphQLQuery;
use App\GraphQL\Installment\Queries\InstallmentPinflCardsGraphQLQuery;
use App\GraphQL\Installment\Queries\InstallmentsGraphQLQuery;
use App\GraphQL\Installment\Queries\SettingsGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleInstallmentGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionTypeGraphQLQuery;
use App\GraphQL\Installment\Queries\StatisticsByBranchesGraphQLQuery;
use App\GraphQL\Installment\Queries\StatisticsGraphQLQuery;
use App\GraphQL\Installment\Queries\StatisticsInfoGraphQLQuery;
use App\GraphQL\Installment\Queries\TariffsGraphQLQuery;
use App\GraphQL\Installment\Queries\TelegramBotSettingsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionAttemptsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionTypesGraphQLQuery;
use App\GraphQL\Installment\Types\BranchForStatisticsGraphQLType;
use App\GraphQL\Installment\Types\CalculatedGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentCommentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\GraphQL\Installment\Types\InstallmentInputGraphQLType;
use App\GraphQL\Installment\Types\SettingsGraphQLType;
use App\GraphQL\Installment\Types\StatisticsByBranchesGraphQLType;
use App\GraphQL\Installment\Types\StatisticsGraphQLType;
use App\GraphQL\Installment\Types\StatisticsInfoGraphQLType;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\GraphQL\Installment\Types\TariffInputGraphQLType;
use App\GraphQL\Installment\Types\TransactionAttemptGraphQLType;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\GraphQL\Installment\Types\TypeTotalGraphQLType;
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
use App\GraphQL\Scoring\Types\ScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestUnionGraphQLType;
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
                TransactionAttemptsGraphQLQuery::class,
                TransactionsGraphQLQuery::class,
                SingleTransactionGraphQLQuery::class,
                SettingsGraphQLQuery::class,
                StatisticsGraphQLQuery::class,
                StatisticsInfoGraphQLQuery::class,
                StatisticsByBranchesGraphQLQuery::class,
                TelegramBotSettingsGraphQLQuery::class,
                InstallmentPinflCardsGraphQLQuery::class,
            ],
            'mutation' => [
                CreateInstallmentGraphQLMutation::class,
                UpdateInstallmentGraphQLMutation::class,
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
                UpdateGuarantorGraphQLMutation::class,
                DetachGuarantorGraphQLMutation::class,
                CreateTransactionGraphQLMutation::class,
                UpdateTransactionGraphQLMutation::class,
                ReverseTransactionGraphQLMutation::class,
                DeleteTransactionGraphQLMutation::class,
                CreateDocumentGraphQLMutation::class,
                DeleteDocumentGraphQLMutation::class,
                ChangeStatusGraphQLMutation::class,
                UpdateInstallmentCustomerAnyCardChargeGraphQLMutation::class,
                ChangeSettingsGraphQLMutation::class,
                UpdateTelegramBotSettingsGraphQLMutation::class,
                UpdateCustomerPinflCardsGraphQLMutation::class,
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
                UniredCardGraphQLType::class,
                AttachmentGraphQLType::class,
                TransactionAttemptGraphQLType::class,
                TransactionGraphQLType::class,
                InstallmentCommentGraphQLType::class,
                InstallmentGuarantorGraphQLType::class,
                SettingsGraphQLType::class,
                StatisticsGraphQLType::class,
                StatisticsInfoGraphQLType::class,
                BranchForStatisticsGraphQLType::class,
                TypeTotalGraphQLType::class,
                StatisticsByBranchesGraphQLType::class,

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
                TagGraphQLType::class,

                // unions last
                ScoringRequestUnionGraphQLType::class,
                ScoringRequestGraphQLType::class,
                UniredCardGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
