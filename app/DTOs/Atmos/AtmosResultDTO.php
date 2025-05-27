<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosResultDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $code,
        public ?string $description,
    ) {}

    public function isSuccess(): bool
    {
        return $this->code == 'OK';
    }

    public function getDescription(): string
    {
        switch ($this->code) {
            case 'STPIMS-ERR-001':
                return __('api.internal_error');
            case 'STPIMS-ERR-004':
                return __('api.card_not_found');
            case 'STPIMS-ERR-006':
                return __('api.missing_parameters');
            case 'STPIMS-ERR-057':
                return __('api.insufficient_funds');
            case 'STPIMS-ERR-058':
                return __('api.card_blocked');
            case 'STPIMS-ERR-067':
                return __('api.card_expired');
            case 'STPIMS-ERR-072':
                return __('api.operations_prohibited');
            case 'STPIMS-ERR-089':
                return __('api.sms_notifications_disabled');
            case 'STPIMS-ERR-112':
                return __('api.low_balance');
            case 'STPIMS-ERR-120':
                return __('api.payment_unavailable');
            case 'INFO-201':
                return __('api.confirmation_code_sent');
            case 'INFO-202':
                return __('api.payment_processing');
            default:
                return $this->description;
        }
    }
}
