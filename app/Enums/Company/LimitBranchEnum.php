<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum LimitBranchEnum: int
{
    use EnumMethods;
    case MULTIBRANCH_1_BRANCH = 1;
    case MULTIBRANCH_2_BRANCH = 2;
    case MULTIBRANCH_3_BRANCH = 3;
    case MULTIBRANCH_4_BRANCH = 5;

}
