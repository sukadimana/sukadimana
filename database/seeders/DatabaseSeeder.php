<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\MataKuliah;
use App\Models\Page;
use App\Models\Post;
use App\Models\Prodi;
use App\Models\ProfilKampus;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kampus.edu'],
            [
                'name' => 'Admin Kampus',
                'password' => 'admin123',
                'role' => UserRole::ADMIN,
            ]
        );

        ProfilKampus::firstOrCreate([], [
            'nama_kampus' => 'SIAKAD Pro',
            'alamat_kampus' => 'Sistem Informasi Akademik Terpadu - Kampus Digital',
            'telepon' => '-',
            'email' => 'admin@kampus.edu',
            'satgas_nama' => 'Satgas PPKS SIAKAD Pro',
            'satgas_deskripsi' => 'Satuan Tugas Pencegahan dan Penanganan Kekerasan Seksual di lingkungan kampus, sesuai Permendikbudristek No. 30 Tahun 2021.',
            'satgas_email' => 'satgas-ppks@kampus.edu',
            'satgas_telepon' => '-',
        ]);

        $mpi = Prodi::firstOrCreate(
            ['kode_prodi' => 'MPI'],
            ['nama_prodi' => 'Manajemen Pendidikan Islam', 'jenjang' => 'S1']
        );

        Prodi::firstOrCreate(
            ['kode_prodi' => 'AS'],
            ['nama_prodi' => 'Ahwal Asy-Syakhshiyyah', 'jenjang' => 'S1']
        );

        Prodi::firstOrCreate(
            ['kode_prodi' => 'MPI-S2'],
            ['nama_prodi' => 'Manajemen Pendidikan Islam', 'jenjang' => 'S2']
        );

        TahunAkademik::firstOrCreate(
            ['nama' => now()->year.'/'.(now()->year + 1).' Ganjil'],
            ['semester_tipe' => 'GANJIL', 'is_active' => true]
        );

        foreach ([
            ['kode_mk' => 'MPI101', 'nama_mk' => 'Pengantar Ilmu Pendidikan', 'sks' => 3, 'semester' => 1],
            ['kode_mk' => 'MPI102', 'nama_mk' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 1],
            ['kode_mk' => 'MPI201', 'nama_mk' => 'Manajemen Kurikulum', 'sks' => 3, 'semester' => 1],
        ] as $mk) {
            MataKuliah::firstOrCreate(
                ['kode_mk' => $mk['kode_mk']],
                ['nama_mk' => $mk['nama_mk'], 'sks' => $mk['sks'], 'prodi_id' => $mpi->id, 'semester' => $mk['semester']]
            );
        }

        Post::firstOrCreate(
            ['channel' => 'kampus', 'slug' => 'selamat-datang-di-website-kampus'],
            [
                'title' => 'Selamat Datang di Website Kampus',
                'excerpt' => 'Website resmi kampus kini hadir untuk menyampaikan berita, kegiatan, dan informasi akademik.',
                'content' => 'Website ini menjadi kanal resmi kampus untuk berbagi berita, pengumuman, dan galeri kegiatan kepada masyarakat luas.',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        Page::firstOrCreate(
            ['channel' => 'kampus', 'slug' => 'profil-kampus'],
            [
                'title' => 'Profil Kampus',
                'content' => 'Kampus kami berkomitmen mencetak generasi unggul melalui pendidikan yang berkualitas dan berkarakter.',
                'is_published' => true,
                'menu_order' => 1,
            ]
        );

        Post::firstOrCreate(
            ['channel' => 'satgas_ppk', 'slug' => 'mengenal-satgas-ppks'],
            [
                'title' => 'Mengenal Satgas PPKS',
                'excerpt' => 'Satgas PPKS hadir sebagai unit penanganan kekerasan seksual di lingkungan kampus.',
                'content' => 'Satgas PPKS bertugas melakukan pencegahan, penanganan, dan pendampingan bagi korban kekerasan seksual di lingkungan kampus sesuai Permendikbudristek No. 30 Tahun 2021.',
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
