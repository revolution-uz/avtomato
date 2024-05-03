<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosCredentialsDTO',
    title: 'Atmos Credentials DTO',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'example'),
        new OA\Property(property: 'password', type: 'string', example: 'example'),
        new OA\Property(property: 'store_id', type: 'int', example: '1'),
        new OA\Property(property: 'expires_in', type: 'int', example: '1'),
        new OA\Property(property: 'access_token', type: 'string', example: 'token'),
    ],
)]
class AtmosCredentialsDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $username,
        public ?string $password,
        public ?int $store_id,
        public ?int $expires_in,
        public ?string $access_token,
        public ?string $token_type = null
    ) {
    }
}
