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
        // Validasi data peminjaman
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota yang dipilih tidak ditemukan.',
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        // Menghitung jumlah buku yang masih dipinjam anggota
        $jumlahDipinjam = Loan::where('member_id', $validated['member_id'])
            ->where('status', 'dipinjam')
            ->count();

        // Membatasi maksimal 3 buku yang sedang dipinjam
        if ($jumlahDipinjam >= 3) {
            return back()->withErrors([
                'member_id' => 'Anggota ini sudah meminjam maksimal 3 buku. Kembalikan buku terlebih dahulu sebelum meminjam lagi.',
            ]);
        }

        // Memeriksa stok buku
        $book = Book::findOrFail($validated['book_id']);

        if ($book->stok < 1) {
            return back()->withErrors([
                'book_id' => 'Stok buku ini sudah habis. Pilih buku lain.',
            ]);
        }

        // Menyimpan peminjaman dan mengurangi stok
        DB::transaction(function () use ($validated, $book) {
            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(7)->toDateString(),
                'status' => 'dipinjam',
            ]);

            $book->decrement('stok');
        });

        // Kembali ke halaman daftar peminjaman
        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil disimpan. Stok buku berkurang 1.');
    }
}
