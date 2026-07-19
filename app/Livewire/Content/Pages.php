<?php

namespace App\Livewire\Content;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Pages extends Component
{
    public string $channel = 'kampus';

    public bool $isModalOpen = false;

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public bool $is_published = true;

    public int $menu_order = 0;

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
        $this->reset(['title', 'slug', 'content', 'editingId']);
        $this->is_published = true;
        $this->menu_order = 0;
        $this->resetErrorBag();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $page = Page::findOrFail($id);
        $this->editingId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content ?? '';
        $this->is_published = $page->is_published;
        $this->menu_order = $page->menu_order;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')->where('channel', $this->channel)->ignore($this->editingId)],
            'content' => 'nullable|string',
        ]);

        $payload = [
            'channel' => $this->channel,
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'content' => $this->content,
            'is_published' => $this->is_published,
            'menu_order' => $this->menu_order,
        ];

        if ($this->editingId) {
            Page::findOrFail($this->editingId)->update($payload);
        } else {
            Page::create($payload);
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Page::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.content.pages', [
            'pages' => Page::channel($this->channel)->orderBy('menu_order')->orderBy('title')->get(),
        ]);
    }
}
