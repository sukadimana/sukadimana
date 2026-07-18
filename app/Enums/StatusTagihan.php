<?php

namespace App\Enums;

enum StatusTagihan: string
{
    case LUNAS = 'LUNAS';
    case BELUM_LUNAS = 'BELUM_LUNAS';
    case CICILAN = 'CICILAN';
}
