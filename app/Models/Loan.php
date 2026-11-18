<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Denda Rp1.000 untuk setiap hari keterlambatan.
    public const DENDA_PER_HARI = 1000;

    // Kolom yang boleh diisi.
    protected $fillable = [
        'book_id',
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    // Atribut virtual yang dikirim ke Vue.
    protected $appends = [
        'hari_terlambat',
        'perkiraan_denda',
    ];

    // Relasi ke tabel buku.
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi ke tabel anggota.
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Menghitung jumlah hari keterlambatan.
    protected function hariTerlambat(): Attribute
    {
        return Attribute::make(get: function () {

            $jatuhTempo = Carbon::parse(
                $this->tanggal_kembali_rencana
            )->startOfDay();

            $acuan = $this->tanggal_kembali
                ? Carbon::parse($this->tanggal_kembali)->startOfDay()
                : today();

            // Jika belum melewati jatuh tempo.
            if ($acuan->lessThanOrEqualTo($jatuhTempo)) {
                return 0;
            }

            // Menghitung selisih hari keterlambatan.
            return (int) $jatuhTempo->diffInDays($acuan);
        });
    }

    // Menghitung perkiraan denda.
    protected function perkiraanDenda(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hari_terlambat * self::DENDA_PER_HARI
        );
    }
}
