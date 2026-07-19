<?php

namespace App\Livewire\Public\Berita;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.public.berita.index', [
            'posts' => Post::channel('kampus')->published()->orderByDesc('published_at')->paginate(9),
        ]);
    }
}
