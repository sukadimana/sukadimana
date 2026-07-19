<?php

namespace App\Livewire\ContactMessages;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $viewingId = null;

    public function view(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);
        $this->viewingId = $id;
    }

    public function close(): void
    {
        $this->viewingId = null;
    }

    public function delete(int $id): void
    {
        ContactMessage::findOrFail($id)->delete();

        if ($this->viewingId === $id) {
            $this->viewingId = null;
        }
    }

    public function render()
    {
        return view('livewire.contact-messages.index', [
            'messages' => ContactMessage::orderByDesc('created_at')->paginate(10),
            'unreadCount' => ContactMessage::where('is_read', false)->count(),
            'viewingMessage' => $this->viewingId ? ContactMessage::find($this->viewingId) : null,
        ]);
    }
}
