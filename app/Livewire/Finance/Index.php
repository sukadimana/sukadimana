<?php

namespace App\Livewire\Finance;

use App\Enums\StatusTagihan;
use App\Models\ActivityLog;
use App\Models\KategoriBeasiswa;
use App\Models\Pembayaran;
use App\Models\Prodi;
use App\Models\ProfilKampus;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public string $filterJenjang = 'all';

    public string $filterProdi = 'all';

    public string $filterKategori = 'all';

    public string $filterStatus = 'all';

    public ?int $selectedTagihanId = null;

    public bool $isPaymentModalOpen = false;

    public $paymentAmount = 0;

    public string $paymentMethod = 'TUNAI';

    public ?int $deleteConfirmId = null;

    public ?int $showReceiptId = null;

    public bool $isReprint = false;

    public ?string $errorMessage = null;

    public function selectTagihan(int $id): void
    {
        $this->selectedTagihanId = $id;
    }

    public function openPaymentModal(): void
    {
        $tagihan = Tagihan::find($this->selectedTagihanId);
        if (! $tagihan) {
            return;
        }
        $this->paymentAmount = (float) $tagihan->sisa_tagihan;
        $this->paymentMethod = 'TUNAI';
        $this->errorMessage = null;
        $this->isPaymentModalOpen = true;
    }

    public function submitPayment(): void
    {
        $tagihan = Tagihan::find($this->selectedTagihanId);
        if (! $tagihan || $this->paymentAmount <= 0) {
            return;
        }

        if ($this->paymentAmount > $tagihan->sisa_tagihan) {
            $this->errorMessage = 'Jumlah bayar tidak boleh melebihi sisa tagihan.';

            return;
        }

        $newTotalPaid = $tagihan->total_sudah_bayar + $this->paymentAmount;
        $newBalance = $tagihan->total_harus_bayar - $newTotalPaid;
        $newStatus = $newBalance <= 0 ? StatusTagihan::LUNAS->value : StatusTagihan::CICILAN->value;

        $payment = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'nomor_transaksi' => 'TRX-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'tanggal_bayar' => now()->toDateString(),
            'jumlah_bayar' => $this->paymentAmount,
            'metode_bayar' => $this->paymentMethod,
        ]);

        $tagihan->update([
            'total_sudah_bayar' => $newTotalPaid,
            'sisa_tagihan' => max(0, $newBalance),
            'status' => $newStatus,
        ]);

        $user = Auth::user();
        ActivityLog::log(
            (string) $user->id,
            $user->name,
            $user->role?->value,
            "Mencatat pembayaran Rp ".number_format($this->paymentAmount, 0, ',', '.')." untuk tagihan {$tagihan->nama_mahasiswa_snapshot}"
        );

        $this->isPaymentModalOpen = false;
        $this->paymentAmount = 0;
        $this->isReprint = false;
        $this->showReceiptId = $payment->id;
    }

    public function confirmDeleteTagihan(): void
    {
        $tagihan = Tagihan::withCount('pembayarans')->find($this->selectedTagihanId);
        if (! $tagihan || $tagihan->pembayarans_count > 0) {
            return;
        }
        $this->deleteConfirmId = $tagihan->id;
    }

    public function deleteTagihan(): void
    {
        if ($this->deleteConfirmId) {
            Tagihan::destroy($this->deleteConfirmId);
            $this->selectedTagihanId = null;
        }
        $this->deleteConfirmId = null;
    }

    public function reprint(int $paymentId): void
    {
        $this->isReprint = true;
        $this->showReceiptId = $paymentId;
    }

    public function render()
    {
        $tagihans = Tagihan::with(['mahasiswa.prodi'])
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('nama_mahasiswa_snapshot', 'like', "%{$this->search}%")
                ->orWhereHas('mahasiswa', fn ($qqq) => $qqq->where('nim', 'like', "%{$this->search}%"))))
            ->when($this->filterJenjang !== 'all', fn ($q) => $q->whereHas('mahasiswa', fn ($qq) => $qq->where('jenjang', $this->filterJenjang)))
            ->when($this->filterProdi !== 'all', fn ($q) => $q->whereHas('mahasiswa', fn ($qq) => $qq->where('prodi_id', $this->filterProdi)))
            ->when($this->filterKategori !== 'all', fn ($q) => $q->whereHas('mahasiswa', fn ($qq) => $qq->where('kategori_beasiswa', $this->filterKategori)))
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderByDesc('created_at')
            ->get();

        $selectedTagihan = $this->selectedTagihanId ? Tagihan::with(['mahasiswa.prodi'])->find($this->selectedTagihanId) : null;
        $relatedPayments = $selectedTagihan ? Pembayaran::where('tagihan_id', $selectedTagihan->id)->orderByDesc('tanggal_bayar')->get() : collect();

        $receipt = $this->showReceiptId ? Pembayaran::with('tagihan.mahasiswa.prodi')->find($this->showReceiptId) : null;

        return view('livewire.finance.index', [
            'tagihans' => $tagihans,
            'selectedTagihan' => $selectedTagihan,
            'relatedPayments' => $relatedPayments,
            'receipt' => $receipt,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'kategoriBeasiswas' => KategoriBeasiswa::all(),
            'profilKampus' => ProfilKampus::first(),
        ]);
    }
}
