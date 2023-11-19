<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $branch_id
 * @property int $tariff_id
 * @property float $amount
 * @property float $initial_fee
 * @property string $description
 * @property bool $sms_notify
 * @property bool $any_card_charge
 * @property bool $is_blacklist
 * @property int $period
 * @property string $plum_id
 * @property Carbon|null $starts_in
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property InstallmentTypeEnum $type
 * @property InstallmentStatusEnum $status
 * @property Branch $branch
 * @property Tariff $tariff
 * @property Customer $customer
 * @property Card[] $cards
 * @property InstallmentGraph[] $graphs
 * @property Attachment[] $attachments
 * @property InstallmentComment[] $comments
 * @property Customer[] $guarantors
 */
class Installment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'type',
        'amount',
        'initial_fee',
        'description',
        'sms_notify',
        'any_card_charge',
        'is_blacklist',
        'status',
        'period',
        'plum_id',
        'starts_in',
        'next_pay_date',
    ];

    protected $casts = [
        'starts_in' => 'date:d-m-Y',
        'next_pay_date' => 'date:d-m-Y',
        'status' => InstallmentStatusEnum::class,
        'type' => InstallmentTypeEnum::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'installment_cards', 'installment_id', 'card_id')->select('cards.*');
    }

    public function guarantors()
    {
        return $this->belongsToMany(Customer::class, 'installment_guarantors', 'installment_id', 'customer_id')->select('customers.*');
    }

    public function attachments()
    {
        return $this->morphToMany(Attachment::class, 'attachmentable');
    }

    public function comments()
    {
        return $this->belongsToMany(InstallmentComment::class);
    }

    public function graphs()
    {
        return $this->belongsToMany(InstallmentGraph::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function start()
    {
        if ($this->status == InstallmentStatusEnum::FROZEN) {
            return $this->changeStatus(InstallmentStatusEnum::RUNNING);
        }
        if ($this->changeStatus(InstallmentStatusEnum::RUNNING)) {
            /** @var Tariff $tariff */
            $tariff = Tariff::findOrFail($this->tariff_id);
            $amount = $this->amount;
            $amount -= $this->initial_fee;
            $portion = $amount * $tariff->percent / 100;

            $currentDate = new Carbon;
            $currentDate->timestamp = $this->starts_in->timestamp;
            $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($this->period - 1));

            foreach ($periods as $period) {
                $installmentGraph = new InstallmentGraph;
                $installmentGraph->installment_id = $this->id;
                $installmentGraph->amount = $amount / $this->period + $portion;
                $installmentGraph->starts_in = Carbon::make($period->timestamp);
                $installmentGraph->status = InstallmentStatusEnum::RUNNING;
                $installmentGraph->save();
            }
        }

        return false;
    }

    public function cancel()
    {
        return $this->changeStatus(InstallmentStatusEnum::CANCELED);
    }

    public function freeze()
    {
        return $this->changeStatus(InstallmentStatusEnum::FROZEN);
    }

    public function finish()
    {
        return $this->changeStatus(InstallmentStatusEnum::FINISHED);
    }

    private function changeStatus(InstallmentStatusEnum $statusEnum)
    {
        if ($this->canBe($statusEnum)) {
            $this->status = $statusEnum;

            return $this->save();
        }

        return false;
    }

    private function canBe(InstallmentStatusEnum $statusEnum)
    {
        return in_array($this->status, InstallmentStatusEnum::getStatusListByStatus($statusEnum));
    }
}
