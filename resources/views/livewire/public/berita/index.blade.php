<div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Berita &amp; Pengumuman</h1>

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

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
