<?php

namespace App\Livewire\Public;

use App\Models\Gallery;
use App\Models\Post;
use App\Models\ProfilKampus;
use App\Models\Prodi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.public.home', [
            'profil' => ProfilKampus::first(),
            'posts' => Post::channel('kampus')->published()->orderByDesc('published_at')->limit(3)->get(),
            'photos' => Gallery::channel('kampus')->orderByDesc('created_at')->limit(8)->get(),
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
        ]);
    }
}
