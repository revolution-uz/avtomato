<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'KatmPresenceReportsDTO',
    title: 'KatmPresenceReportsDTO',
    properties: [
        new OA\Property(property: 'presence_report', type: 'array', items: new OA\Items(ref: '#/components/schemas/KatmPresenceReportDTO')),
    ],
)]
class KatmPresenceReportsDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmPresenceReportDTO::class)]
        public ?DataCollection $presence_report,
    ) {
    }
}
