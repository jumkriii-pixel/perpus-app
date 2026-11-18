<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

// Data anggota dan buku dari LoanController
defineProps({
    members: Array,
    books: Array,
})

// Data formulir peminjaman
const form = useForm({
    member_id: '',
    book_id: '',
})

// Mengirim data peminjaman ke server
function submit() {
    form.post(route('loans.store'))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Pinjam Buku</h1>

            <p class="mt-1 text-gray-600">
                Isi formulir berikut untuk melakukan peminjaman buku.
            </p>

            <form
                @submit.prevent="submit"
                class="mt-4 max-w-xl space-y-4"
            >
                <!-- Pilihan Anggota -->
                <div>
                    <label class="mb-1 block font-medium">
                        Anggota
                    </label>

                    <select
                        v-model="form.member_id"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="" disabled>
                            -- Pilih anggota --
                        </option>

                        <option
                            v-for="member in members"
                            :key="member.id"
                            :value="member.id"
                        >
                            {{ member.nama }} ({{ member.nis_nim }})
                        </option>
                    </select>

                    <div
                        v-if="form.errors.member_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.member_id }}
                    </div>
                </div>

                <!-- Pilihan Buku -->
                <div>
                    <label class="mb-1 block font-medium">
                        Buku
                    </label>

                    <select
                        v-model="form.book_id"
                        class="w-full rounded-lg border-gray-300"
                    >
                        <option value="" disabled>
                            -- Pilih buku --
                        </option>

                        <option
                            v-for="book in books"
                            :key="book.id"
                            :value="book.id"
                        >
                            {{ book.judul }} (Kode: {{ book.kode_buku }}) - Stok: {{ book.stok }}
                        </option>
                    </select>

                    <div
                        v-if="form.errors.book_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.book_id }}
                    </div>

                    <!-- Pemberitahuan stok habis -->
                    <div
                        v-if="books.length === 0"
                        class="mt-2 text-sm text-yellow-700"
                    >
                        Belum ada buku dengan stok tersedia.
                    </div>
                </div>

                <!-- Informasi Tanggal -->
                <div class="rounded-lg bg-blue-50 p-3 text-sm text-blue-800">
                    Tanggal pinjam adalah hari ini. Jatuh tempo otomatis
                    14 hari setelah tanggal pinjam.
                </div>

                <!-- Tombol -->
                <div class="flex gap-2">
                    <button
                        type="submit"
                        :disabled="form.processing || books.length === 0"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Peminjaman' }}
                    </button>

                    <Link
                        href="/loans"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300"
                    >
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
