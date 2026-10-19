<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Membaca kata kunci pencarian dari query string (?search=...)
        $books = Book::when($request->search, function ($query, $search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); // Memastikan kata kunci pencarian tetap terbawa saat berpindah halaman paginasi

        return Inertia::render('Books/Index', [
            'books'  => $books,
            'search' => $request->search, // Dikirim balik ke Vue agar nilai input pencarian tetap tersimpan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Books/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data input dari form tambah buku
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books',
            'judul'     => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit'  => 'nullable|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        // Simpan data buku baru ke database
        Book::create($validated);

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        // Validasi data input dengan mengabaikan ID buku yang sedang diperbarui
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
            'judul'     => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit'  => 'nullable|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        // Perbarui data buku di database
        $book->update($validated);

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // Hapus data buku dari database
        $book->delete();

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus');
    }
}