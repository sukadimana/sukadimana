<?php

namespace App\Livewire\Satgas\Public\Berita;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.satgas')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.satgas.public.berita.index', [
            'posts' => Post::channel('satgas_ppk')->published()->orderByDesc('published_at')->paginate(9),
        ]);
    }
}
