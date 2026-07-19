<?php

namespace App\Livewire\Public\Galeri;

use App\Models\Gallery;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.public.galeri.index', [
            'photos' => Gallery::channel('kampus')->orderByDesc('created_at')->paginate(16),
        ]);
    }
}
