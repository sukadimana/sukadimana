<?php

namespace App\Livewire\Public\ProgramStudi;

use App\Models\Prodi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.public.program-studi.index', [
            'prodis' => Prodi::orderBy('jenjang')->orderBy('nama_prodi')->get(),
        ]);
    }
}
