<?php

namespace App\Models\Company;

use App\Enums\GenderEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string|null $pinfl
 * @property string|null $tin
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property Carbon|null $passport_given_date
 * @property Carbon|null $passport_expiration_date
 * @property string|null $passport_given_place
 * @property string|null $passport_citizenship
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property Carbon|null $birth_date
 * @property string|null $birth_place
 * @property GenderEnum|null $gender
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property Attachment[] $attachments
 * @property Address[]|Collection<int,Address> $addresses
 * @property Phone[]|Collection<int,Phone> $phones
 * @property Card[]|Collection<int,Phone> $cards
 * @property-read  string $full_name
 *
 * @method static Builder|static query()
 * @method static Customer find(int $id)
 * @method static Customer findOrFail(int $id)
 * @method static Customer first()
 * @method static Customer firstOrFail()
 * @method Customer saveOrFail(array $array = [])
 */
class Customer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes, TenantConnection;

    protected $fillable = [
        'pinfl',
        'tin',
        'passport_series',
        'passport_number',
        'passport_given_date',
        'passport_expiration_date',
        'passport_given_place',
        'passport_citizenship',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'birth_place',
        'gender',
    ];

    protected $casts = [
        'passport_given_date' => 'date:d-m-Y',
        'passport_expiration_date' => 'date:d-m-Y',
        'birth_date' => 'date:d-m-Y',
        'gender' => GenderEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $customer) {
            $scoringRequests = ScoringRequest::query()->whereNull('customer_id')->when(function ($query) use ($customer) {
                $query->when(function ($query) use ($customer) {
                    $query->where('passport_series', $customer->passport_series)
                        ->where('passport_number', $customer->passport_number);
                })->orWhere('pinfl', $customer->pinfl);
            })->get();
            foreach ($scoringRequests as $scoringRequest) {
                $scoringRequest->customer_id = $customer->id;
                $scoringRequest->save();
            }
        });
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable')
            ->withPivot(['id', 'type', 'comment', 'created_at', 'updated_at']); //->withTimestamps();
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function fullName(): Attribute
    {
        return Attribute::make(get: fn () => $this->last_name . ' ' . $this->first_name);
    }
}
