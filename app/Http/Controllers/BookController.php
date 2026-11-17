<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku dan pencarian biasa.
     */
    public function index(Request $request)
    {
        $books = Book::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Books/Index', [
            'books' => $books,
            'search' => $request->search,
        ]);
    }

    /**
     * Menampilkan form tambah buku.
     */
    public function create()
    {
        return Inertia::render('Books/Create');
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku',
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Book $book)
    {
        return Inertia::render('Books/Show', [
            'book' => $book,
        ]);
    }

    /**
     * Menampilkan form edit buku.
     */
    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Endpoint JSON untuk live search.
     */
    public function search(Request $request)
    {
        $kataKunci = $request->query('q', '');

        $books = Book::where(function ($query) use ($kataKunci) {
            $query->where('judul', 'like', "%{$kataKunci}%")
                ->orWhere('pengarang', 'like', "%{$kataKunci}%")
                ->orWhere('penerbit', 'like', "%{$kataKunci}%");
        })
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'kode_buku',
                'judul',
                'pengarang',
                'penerbit',
                'stok',
            ]);

        return response()->json($books);
    }

    /**
     * Endpoint JSON untuk validasi realtime kode buku.
     */
    public function cekKode(Request $request)
    {
        $kode = trim((string) $request->query('kode', ''));

        $sudahDipakai = Book::where('kode_buku', $kode)->exists();

        return response()->json([
            'tersedia' => $kode !== '' && !$sudahDipakai,
        ]);
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
            'judul' => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit' => 'nullable|max:255',
            'tahun' => 'nullable|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui');
    }

    /**
     * Menghapus data buku.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus');
    }
}
