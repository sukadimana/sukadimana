<?php

namespace App\Livewire\Content;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Posts extends Component
{
    use WithFileUploads, WithPagination;

    public string $channel = 'kampus';

    public string $search = '';

    public bool $isModalOpen = false;

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    #[Validate('required|string')]
    public string $content = '';

    public $newImage = null;

    public ?string $existingImage = null;

    public bool $is_published = true;

    public function mount(string $channel = 'kampus'): void
    {
        $this->channel = $channel;
    }

    public function updatedTitle(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->title);
        }
    }

    protected function resetForm(): void
    {
        $this->reset(['title', 'slug', 'excerpt', 'content', 'newImage', 'existingImage', 'editingId']);
        $this->is_published = true;
        $this->resetErrorBag();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->existingImage = $post->image;
        $this->is_published = $post->is_published;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->where('channel', $this->channel)->ignore($this->editingId)],
            'content' => 'required|string',
        ]);

        $payload = [
            'channel' => $this->channel,
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'is_published' => $this->is_published,
            'published_at' => now(),
        ];

        if ($this->newImage) {
            $payload['image'] = $this->newImage->store('posts', 'public');
        }

        if ($this->editingId) {
            Post::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['author_id'] = Auth::id();
            Post::create($payload);
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
    }

    public function render()
    {
        $posts = Post::channel($this->channel)
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.content.posts', [
            'posts' => $posts,
            'channelLabel' => $this->channel === 'satgas_ppk' ? 'Satgas PPK' : 'Kampus',
        ]);
    }
}
