<?php

namespace App\Livewire\Satgas\Public\Halaman;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.satgas')]
class Show extends Component
{
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::channel('satgas_ppk')->published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.satgas.public.halaman.show');
    }
}
