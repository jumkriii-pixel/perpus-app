<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman buku.
     */
    public function index()
    {
        $loans = Loan::with(['member', 'book'])
            ->orderByDesc('id')
            ->paginate(10);

        return inertia('Loans/Index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Menampilkan form peminjaman buku.
     */
    public function create()
    {
        $members = Member::orderBy('nama')
            ->get(['id', 'nama', 'nis_nim']);

        $books = Book::where('stok', '>', 0)
            ->orderBy('judul')
            ->get(['id', 'judul', 'kode_buku', 'stok']);

        return inertia('Loans/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    /**
     * Menyimpan data peminjaman buku.
     */
    public function store(Request $request)
    {
        // Validasi data peminjaman.
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota yang dipilih tidak ditemukan.',
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        // Menyimpan peminjaman dan mengurangi stok.
        DB::transaction(function () use ($validated) {

            // Mengunci data buku agar stok tidak berubah
            // secara bersamaan oleh transaksi lain.
            $book = Book::whereKey($validated['book_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Memeriksa stok buku.
            if ($book->stok < 1) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'book_id' => 'Stok buku ini sudah habis. Pilih buku lain.',
                ]);
            }

            // Menghitung jumlah buku yang masih dipinjam anggota.
            $jumlahDipinjam = Loan::where(
                'member_id',
                $validated['member_id']
            )
                ->where('status', 'dipinjam')
                ->count();

            // Maksimal 3 buku yang sedang dipinjam.
            if ($jumlahDipinjam >= 3) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'member_id' => 'Anggota ini sudah meminjam maksimal 3 buku. Kembalikan buku terlebih dahulu sebelum meminjam lagi.',
                ]);
            }

            // Membuat data peminjaman.
            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(7)->toDateString(),
                'status' => 'dipinjam',
                'denda' => 0,
            ]);

            // Mengurangi stok buku sebanyak 1.
            $book->decrement('stok');
        });

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil disimpan. Stok buku berkurang 1.');
    }

    /**
     * Memproses pengembalian buku dan menghitung denda.
     */
    public function kembalikan(Loan $loan)
    {
        // Memeriksa apakah buku sudah dikembalikan.
        if ($loan->status === 'dikembalikan') {
            return back()->withErrors([
                'loan' => 'Buku ini sudah dikembalikan sebelumnya.',
            ]);
        }

        // Menghitung keterlambatan sebelum tanggal kembali diisi.
        $hariTerlambat = $loan->hari_terlambat;
        $denda = $loan->perkiraan_denda;

        // Memproses pengembalian dan penambahan stok.
        DB::transaction(function () use ($loan, $denda) {

            // Mengunci data peminjaman.
            $loan = Loan::whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Mencegah pengembalian ganda.
            if ($loan->status === 'dikembalikan') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'loan' => 'Buku ini sudah dikembalikan sebelumnya.',
                ]);
            }

            // Mengubah status peminjaman.
            $loan->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => today()->toDateString(),
                'denda' => $denda,
            ]);

            // Menambahkan stok buku sebanyak 1.
            Book::whereKey($loan->book_id)
                ->increment('stok');
        });

        // Membuat pesan sukses.
        $pesan = 'Buku berhasil dikembalikan. Stok buku bertambah 1.';

        if ($denda > 0) {
            $pesan .= ' Terlambat ' . $hariTerlambat .
                ' hari, denda Rp' .
                number_format($denda, 0, ',', '.') . '.';
        }

        return back()->with('success', $pesan);
    }
}
