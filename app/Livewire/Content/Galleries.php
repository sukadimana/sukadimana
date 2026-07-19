<?php

namespace App\Livewire\Content;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Galleries extends Component
{
    use WithFileUploads;

    public string $channel = 'kampus';

    public bool $isModalOpen = false;

    public string $title = '';

    public $newImage = null;

    public ?int $deleteConfirmId = null;

    public function mount(string $channel = 'kampus'): void
    {
        $this->channel = $channel;
    }

    public function openCreate(): void
    {
        $this->reset(['title', 'newImage']);
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'nullable|string|max:255',
            'newImage' => 'required|image|max:5120',
        ]);

        Gallery::create([
            'channel' => $this->channel,
            'title' => $this->title,
            'image' => $this->newImage->store('gallery', 'public'),
        ]);

        $this->isModalOpen = false;
        $this->reset(['title', 'newImage']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteConfirmId = $id;
    }

    public function delete(): void
    {
        if ($this->deleteConfirmId) {
            $photo = Gallery::find($this->deleteConfirmId);
            if ($photo) {
                Storage::disk('public')->delete($photo->image);
                $photo->delete();
            }
        }
        $this->deleteConfirmId = null;
    }

    public function render()
    {
        return view('livewire.content.galleries', [
            'photos' => Gallery::channel($this->channel)->orderByDesc('created_at')->get(),
        ]);
    }
}
