<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case KEUANGAN = 'keuangan';
    case PERPUS = 'perpus';
    case AKADEMIK = 'akademik';
    case MAHASISWA = 'mahasiswa';
    case PETUGAS_YUDISIUM = 'petugas_yudisium';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin Kampus',
            self::KEUANGAN => 'Keuangan',
            self::PERPUS => 'Petugas Perpustakaan',
            self::AKADEMIK => 'Akademik',
            self::MAHASISWA => 'Mahasiswa',
            self::PETUGAS_YUDISIUM => 'Petugas Yudisium',
        };
    }
}
