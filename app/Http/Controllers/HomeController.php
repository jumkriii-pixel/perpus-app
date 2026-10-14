<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $judul = 'Sistem Informasi Peminjaman Buku Perpustakaan Mini';

        return view('home', compact('judul'));
    }

    public function tentang()
    {
        return view('tentang');
    }
}
