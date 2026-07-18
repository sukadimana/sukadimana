<?php

namespace App\Enums;

enum StatusAkademik: string
{
    case AKTIF = 'AKTIF';
    case CUTI = 'CUTI';
    case NON_AKTIF = 'NON-AKTIF';
    case DO = 'DO';
    case LULUS = 'LULUS';
}
