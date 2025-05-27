<?php

namespace App\Enums\Company\Scoring;

enum ScoringRequestStatusEnum: int
{
    case CREATED = 0;
    case WAITING = 1;
    case SUCCESS = 2;
    case FAILED = -1;
}
