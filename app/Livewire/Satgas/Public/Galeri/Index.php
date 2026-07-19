<?php

namespace App\Livewire\Satgas\Public\Galeri;

use App\Models\Gallery;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.satgas')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.satgas.public.galeri.index', [
            'photos' => Gallery::channel('satgas_ppk')->orderByDesc('created_at')->paginate(16),
        ]);
    }
}
