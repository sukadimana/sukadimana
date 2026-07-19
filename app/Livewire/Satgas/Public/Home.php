<?php

namespace App\Livewire\Satgas\Public;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\ProfilKampus;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.satgas')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.satgas.public.home', [
            'profil' => ProfilKampus::first(),
            'posts' => Post::channel('satgas_ppk')->published()->orderByDesc('published_at')->limit(3)->get(),
            'photos' => Gallery::channel('satgas_ppk')->orderByDesc('created_at')->limit(8)->get(),
        ]);
    }
}
