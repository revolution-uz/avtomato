<?php

namespace App\Models\Company;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property GenderEnum|null $gender
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property Attachment[] $attachments
 * @property Address[] $addresses
 * @property Phone[] $phones
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
        'gender',
    ];

    protected $casts = [
        'passport_given_date' => 'date',
        'passport_expiration_date' => 'date',
        'birth_date' => 'date',
        'gender' => GenderEnum::class,
    ];

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
