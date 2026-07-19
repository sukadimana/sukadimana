<?php

namespace App\Livewire\Satgas;

use App\Models\PpksReport;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    public string $filterStatus = 'all';

    public ?int $viewingId = null;

    #[Validate('required|in:BARU,DIPROSES,SELESAI')]
    public string $status = 'BARU';

    public string $admin_notes = '';

    public function view(int $id): void
    {
        $report = PpksReport::findOrFail($id);
        $this->viewingId = $report->id;
        $this->status = $report->status;
        $this->admin_notes = $report->admin_notes ?? '';
        $this->resetErrorBag();
    }

    public function close(): void
    {
        $this->viewingId = null;
    }

    public function save(): void
    {
        $this->validate();

        PpksReport::findOrFail($this->viewingId)->update([
            'status' => $this->status,
            'admin_notes' => $this->admin_notes,
        ]);

        $this->viewingId = null;
    }

    public function render()
    {
        $reports = PpksReport::when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.satgas.reports', [
            'reports' => $reports,
            'viewingReport' => $this->viewingId ? PpksReport::find($this->viewingId) : null,
        ]);
    }
}
