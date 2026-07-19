<?php

namespace App\Livewire\Public\Halaman;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Show extends Component
{
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::channel('kampus')->published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.halaman.show');
    }
}
