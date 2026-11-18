<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class DashboardController extends Controller
{
    /**
     * Menyiapkan angka ringkasan dan data grafik Dashboard.
     */
    public function index()
    {
        // BAGIAN 1: ANGKA RINGKASAN

        // Menghitung jumlah buku
        $totalBuku = Book::count();

        // Menghitung seluruh stok buku
        $totalStok = (int) Book::sum('stok');

        // Menghitung jumlah anggota
        $totalAnggota = Member::count();

        // Peminjaman yang belum dikembalikan
        $sedangDipinjam = Loan::where('status', 'dipinjam')->count();

        // Menghitung peminjaman yang terlambat
        $terlambat = Loan::where('status', 'dipinjam')
            ->where(
                'tanggal_kembali_rencana',
                '<',
                today()->toDateString()
            )
            ->count();

        // Menghitung seluruh denda
        $totalDenda = (int) Loan::sum('denda');


        // BAGIAN 2: DATA GRAFIK 6 BULAN TERAKHIR

        $namaBulan = [
            'Jan', 'Feb', 'Mar', 'Apr',
            'Mei', 'Jun', 'Jul', 'Agu',
            'Sep', 'Okt', 'Nov', 'Des'
        ];

        $label = [];
        $jumlah = [];

        // Mengambil data 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {

            // Tanggal awal bulan
            $awal = today()->startOfMonth()->subMonths($i);

            // Tanggal akhir bulan
            $akhir = $awal->copy()->endOfMonth();

            // Label bulan untuk grafik
            $label[] = $namaBulan[$awal->month - 1]
                . ' ' . $awal->year;

            // Menghitung jumlah peminjaman setiap bulan
            $jumlah[] = Loan::whereBetween('tanggal_pinjam', [
                $awal->toDateString(),
                $akhir->toDateString(),
            ])->count();
        }


        // BAGIAN 3: MENGIRIM DATA KE DASHBOARD.VUE

        return inertia('Dashboard', [
            'statistik' => [
                'total_buku' => $totalBuku,
                'total_stok' => $totalStok,
                'total_anggota' => $totalAnggota,
                'sedang_dipinjam' => $sedangDipinjam,
                'terlambat' => $terlambat,
                'total_denda' => $totalDenda,
            ],

            'grafik' => [
                'label' => $label,
                'jumlah' => $jumlah,
            ],
        ]);
    }
}
