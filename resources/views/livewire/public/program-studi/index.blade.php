<div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Program Studi</h1>

    @if ($prodis->isEmpty())
        <p class="text-gray-500">Belum ada data program studi.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($prodis as $prodi)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold mb-3">{{ $prodi->jenjang }}</span>
                    <p class="font-bold text-gray-900 text-lg">{{ $prodi->nama_prodi }}</p>
                    <p class="text-xs text-gray-400 mt-1 font-mono">{{ $prodi->kode_prodi }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
