<?php

namespace App\Enums\External;

enum AsaxiyRequestStatusEnum: int
{
    case WAITING = 1;
    case IN_PROCESS = 10;
    case SUCCESS = 20;
}
