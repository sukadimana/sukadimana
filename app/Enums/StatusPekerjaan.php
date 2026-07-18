<?php

namespace App\Enums;

enum StatusPekerjaan: string
{
    case KERJA = 'KERJA';
    case WIRAUSAHA = 'WIRAUSAHA';
    case LANJUT_STUDI = 'LANJUT_STUDI';
    case BELUM_KERJA = 'BELUM_KERJA';
}
