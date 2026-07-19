<div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Galeri Kegiatan</h1>

    @if ($photos->isEmpty())
        <p class="text-gray-500">Belum ada foto di galeri.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($photos as $photo)
                <div class="rounded-xl overflow-hidden border border-gray-200 aspect-square">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->image) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $photos->links() }}
        </div>
    @endif
</div>
