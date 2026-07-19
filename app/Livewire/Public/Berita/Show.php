<?php

namespace App\Livewire\Public\Berita;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Show extends Component
{
    public Post $post;

    public function mount(string $slug): void
    {
        $this->post = Post::channel('kampus')->published()->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.public.berita.show', [
            'latest' => Post::channel('kampus')->published()->where('id', '!=', $this->post->id)->orderByDesc('published_at')->limit(4)->get(),
        ]);
    }
}
