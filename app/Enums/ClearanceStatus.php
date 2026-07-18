<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    case APPROVED = 'APPROVED';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
}
