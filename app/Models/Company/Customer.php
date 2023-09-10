<?php

namespace App\Models\Company;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
 * @property string|null $address
 * @property GenderEnum|null $gender
 * @property int|null $region_id
 * @property int|null $district_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property Region|null $region
 * @property District|null $district
 * @property Mahalla|null $mahalla
 */
class Customer extends Model
{
    use SoftDeletes;

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
        'address',
        'gender',
        'region_id',
        'district_id',
        'mahalla_id',
    ];

    protected $casts = [
        'passport_given_date' => 'date',
        'passport_expiration_date' => 'date',
        'birth_date' => 'date',
        'gender' => GenderEnum::class,
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mahalla(): BelongsTo
    {
        return $this->belongsTo(Mahalla::class);
    }
}
