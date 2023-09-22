<?php

namespace App\Enums;

enum SettingsType: int
{
    case BOOLEAN = 1;
    case INT = 2;
    case STRING = 3;
    case TEXT = 4;
}
