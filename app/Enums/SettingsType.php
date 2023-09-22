<?php

namespace App\Enums;

enum SettingsType: int
{
    case BOOLEAN = 1;
    case FLOAT = 2;
    case INT = 3;
    case STRING = 4;
    case TEXT = 5;
}
