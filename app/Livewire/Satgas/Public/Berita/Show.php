<?php

namespace App\Livewire\Satgas\Public\Berita;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.satgas')]
class Show extends Component
{
    public Post $post;

    public function mount(string $slug): void
    {
        $this->post = Post::channel('satgas_ppk')->published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.satgas.public.berita.show', [
            'latest' => Post::channel('satgas_ppk')->published()->where('id', '!=', $this->post->id)->orderByDesc('published_at')->limit(4)->get(),
        ]);
    }
}
