<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Pencarian berdasarkan nama, NIS/NIM, atau No. HP
        $members = Member::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis_nim', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        })->latest()->paginate(10)->withQueryString();

        return inertia('Members/Index', [
            'members' => $members,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return inertia('Members/Create');
    }

    public function store(Request $request)
    {
        // Validasi data anggota dan foto
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'nis_nim' => 'required|max:50|unique:members',
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Simpan foto jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('members', 'public');
        }

        // Simpan data anggota
        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Member $member)
    {
        //
    }

    public function edit(Member $member)
    {
        return inertia('Members/Edit', [
            'member' => $member,
        ]);
    }

    public function update(Request $request, Member $member)
    {
        // Validasi data anggota dan foto
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'nis_nim' => 'required|max:50|unique:members,nis_nim,' . $member->id,
            'no_hp' => 'nullable|digits_between:8,15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Jika ada foto baru
        if ($request->hasFile('foto')) {

            // Hapus foto lama jika ada
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }

            // Simpan foto baru
            $validated['foto'] = $request->file('foto')->store('members', 'public');

        } else {
            // Pertahankan foto lama
            unset($validated['foto']);
        }

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui');
    }

    public function destroy(Member $member)
    {
        // Hapus foto dari storage jika ada
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        // Hapus data anggota
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus');
    }
}
