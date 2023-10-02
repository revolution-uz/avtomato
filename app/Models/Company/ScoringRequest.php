<?php

namespace App\Models\Company;

use App\Casts\ScoringRequestCast;
use App\Casts\ScoringResponseCast;
use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\Enums\ClientCodeEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class ScoringRequest
 *
 * @property int $id
 * @property int|null $ext_id
 * @property ScoringRequestStatusEnum $status
 * @property ScoringRequestTypeEnum $type
 * @property ClientCodeEnum $client_code
 * @property int|null $customer_id
 * @property int|null $card_id
 * @property BaseScoringRequest $request
 * @property BaseScoringResponse|null $response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ScoringRequest extends Model
{
    use SoftDeletes;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'ext_id',
        'status',
        'type',
        'client_code',
        'customer_id',
        'card_id',
        'request',
        'response',
    ];

    protected $casts = [
        'status' => ScoringRequestStatusEnum::class,
        'type' => ScoringRequestTypeEnum::class,
        'client_code' => ClientCodeEnum::class,
        'request' => ScoringRequestCast::class,
        'response' => ScoringResponseCast::class,
    ];
}
