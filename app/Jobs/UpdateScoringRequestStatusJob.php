<?php

namespace App\Jobs;

use App\Clients\AsaxiyClient;
use App\Database\DatabaseConnector;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\KatmService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateScoringRequestStatusJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 10;

    private int $tries = 1;

    public function __construct(private readonly int $companyId, private readonly int $scoringRequestId)
    {
    }

    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        DatabaseConnector::setCompanyConnection($this->companyId);

        /** @var ScoringRequest $scoringRequest */
        $scoringRequest = ScoringRequest::query()->findOrFail($this->scoringRequestId);

        if ($scoringRequest->status === ScoringRequestStatusEnum::WAITING) {
            if (
                in_array(
                    $scoringRequest->type,
                    [
                        ScoringRequestTypeEnum::KATM_CREDIT_HISTORY,
                        ScoringRequestTypeEnum::KATM_INPS_HISTORY,
                        ScoringRequestTypeEnum::KATM_PENSION_INFO,
                    ]
                )
            ) {
                $katmService = new KatmService(new AsaxiyClient());
                $katmService->syncRemoteStatus($scoringRequest);
            }
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'update-scoring-request-status-' . $this->companyId . '-' . $this->scoringRequestId;
    }
}
