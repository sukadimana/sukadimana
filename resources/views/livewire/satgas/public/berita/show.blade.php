<div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
    <a href="{{ route('satgas.public.berita.index') }}" class="text-red-700 text-sm font-medium hover:underline">&larr; Kembali ke Informasi</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-2">{{ $post->title }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $post->published_at?->format('d F Y') }}</p>

    @if ($post->image)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover rounded-xl mb-8">
    @endif

    <div class="prose max-w-none text-gray-800 whitespace-pre-line leading-relaxed">{{ $post->content }}</div>

    @if ($latest->isNotEmpty())
        <div class="mt-16 border-t border-gray-200 pt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Lainnya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($latest as $item)
                    <a href="{{ route('satgas.public.berita.show', $item->slug) }}" class="block p-4 bg-white border border-gray-200 rounded-lg hover:shadow-sm transition-shadow">
                        <p class="text-xs text-gray-400 mb-1">{{ $item->published_at?->format('d M Y') }}</p>
                        <p class="font-semibold text-gray-900">{{ $item->title }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
