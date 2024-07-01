<?php

namespace App\Models\Company;

use App\Casts\ScoringRequestCast;
use App\Casts\ScoringResponseCast;
use App\DTOs\Customer\CustomerPassportDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
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
    use LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'status', 'created_at'];

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $scoringRequest) {
            if (is_null($scoringRequest->card_id)) {
                if ($scoringRequest->request instanceof CardCreateRequestDTO) {
                    $card = Card::query()->where('number', Crypt::encryptString($scoringRequest->request->number))->first();
                    if ($card) {
                        $scoringRequest->card_id = $card->id;
                        $scoringRequest->customer_id = $card->customer_id;
                    }
                }
            }
            if (is_null($scoringRequest->customer_id)) {
                if ($scoringRequest->request instanceof KatmCreateRequestDTO || $scoringRequest->request instanceof MibInfoCreateRequestDTO || $scoringRequest->request instanceof TaxInfoCreateRequestDTO) {
                    $customerByPassport = CustomerPassportDTO::from($scoringRequest->request);
                    $customer = Customer::query()->when(
                        $customerByPassport->passport_series && $customerByPassport->passport_number,
                        fn ($query) => $query
                            ->where('passport_series', $customerByPassport->passport_series)
                            ->where('passport_number', $customerByPassport->passport_number)
                    )->orWhere('pinfl', $customerByPassport->pinfl)->first();

                    if ($customer) {
                        $scoringRequest->customer_id = $customer->id;
                    }
                }
            }
        });
    }
}
