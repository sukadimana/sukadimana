<div>
    <section class="bg-gradient-to-br from-red-800 to-red-950 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center">
            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-6">
                <x-icon name="shield" class="w-8 h-8" />
            </div>
            <h1 class="text-3xl sm:text-5xl font-bold mb-4">{{ $profil->satgas_nama ?? 'Satgas PPKS' }}</h1>
            <p class="text-red-100 max-w-2xl mx-auto text-lg">{{ $profil->satgas_deskripsi ?? 'Satuan Tugas Pencegahan dan Penanganan Kekerasan Seksual di lingkungan kampus.' }}</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('satgas.public.pengaduan') }}" class="px-6 py-3 bg-white text-red-800 font-bold rounded-lg hover:bg-red-50 transition-colors">Ajukan Pengaduan</a>
                <a href="{{ route('satgas.public.berita.index') }}" class="px-6 py-3 bg-red-700 border border-red-500 font-bold rounded-lg hover:bg-red-600 transition-colors">Baca Informasi Terbaru</a>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="bg-red-50 border border-red-100 rounded-xl p-6 flex flex-col sm:flex-row items-center gap-4 justify-between">
            <div>
                <p class="font-bold text-red-800">Butuh bantuan segera?</p>
                <p class="text-sm text-red-700">Hubungi kami melalui email {{ $profil->satgas_email ?? '-' }} atau telepon {{ $profil->satgas_telepon ?? '-' }}.</p>
            </div>
            <a href="{{ route('satgas.public.pengaduan') }}" class="whitespace-nowrap px-5 py-2.5 bg-red-700 text-white font-bold rounded-lg hover:bg-red-800 transition-colors">Formulir Pengaduan</a>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Informasi &amp; Kegiatan Terbaru</h2>
            <a href="{{ route('satgas.public.berita.index') }}" class="text-red-700 font-medium hover:underline text-sm">Lihat Semua &rarr;</a>
        </div>
        @if ($posts->isEmpty())
            <p class="text-gray-500">Belum ada informasi yang dipublikasikan.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <a href="{{ route('satgas.public.berita.show', $post->slug) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        @if ($post->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
                        @else
                            <div class="w-full h-44 bg-red-50 flex items-center justify-center text-red-200">
                                <x-icon name="image" class="w-10 h-10" />
                            </div>
                        @endif
                        <div class="p-5">
                            <p class="text-xs text-gray-400 mb-1">{{ $post->published_at?->format('d M Y') }}</p>
                            <h3 class="font-bold text-gray-900 mb-2">{{ $post->title }}</h3>
                            <p class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($post->excerpt, 90) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    @if ($photos->isNotEmpty())
        <section class="bg-white border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Galeri Kegiatan</h2>
                    <a href="{{ route('satgas.public.galeri') }}" class="text-red-700 font-medium hover:underline text-sm">Lihat Semua &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($photos as $photo)
                        <div class="aspect-square rounded-xl overflow-hidden border border-gray-200">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->image) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
