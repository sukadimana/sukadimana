<div>
    <section class="bg-gradient-to-br from-blue-700 to-blue-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center">
            <h1 class="text-3xl sm:text-5xl font-bold mb-4">{{ $profil->nama_kampus ?? 'Selamat Datang' }}</h1>
            <p class="text-blue-100 max-w-2xl mx-auto text-lg">{{ $profil->alamat_kampus ?? 'Membangun generasi unggul melalui pendidikan berkualitas.' }}</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('public.program-studi') }}" class="px-6 py-3 bg-white text-blue-700 font-bold rounded-lg hover:bg-blue-50 transition-colors">Lihat Program Studi</a>
                <a href="{{ route('public.berita.index') }}" class="px-6 py-3 bg-blue-600 border border-blue-400 font-bold rounded-lg hover:bg-blue-500 transition-colors">Baca Berita Terbaru</a>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Berita &amp; Pengumuman Terbaru</h2>
            <a href="{{ route('public.berita.index') }}" class="text-blue-600 font-medium hover:underline text-sm">Lihat Semua &rarr;</a>
        </div>
        @if ($posts->isEmpty())
            <p class="text-gray-500">Belum ada berita yang dipublikasikan.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <a href="{{ route('public.berita.show', $post->slug) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        @if ($post->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
                        @else
                            <div class="w-full h-44 bg-blue-50 flex items-center justify-center text-blue-300">
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

    <section class="bg-white border-y border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Program Studi</h2>
                <a href="{{ route('public.program-studi') }}" class="text-blue-600 font-medium hover:underline text-sm">Lihat Semua &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @forelse ($prodis as $prodi)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold mb-2">{{ $prodi->jenjang }}</span>
                        <p class="font-semibold text-gray-900">{{ $prodi->nama_prodi }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-full">Belum ada data program studi.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if ($photos->isNotEmpty())
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Galeri Kampus</h2>
                <a href="{{ route('public.galeri') }}" class="text-blue-600 font-medium hover:underline text-sm">Lihat Semua &rarr;</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ($photos as $photo)
                    <div class="aspect-square rounded-xl overflow-hidden border border-gray-200">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->image) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
