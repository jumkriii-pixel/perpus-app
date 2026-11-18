<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'

// Membuat form tambah buku
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
})

// Fungsi menyimpan buku
function submit() {
    form.post(route('books.store'))
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="text-xl font-bold">Tambah Buku</h1>

            <p class="mt-1 text-gray-600">
                Silakan isi data buku yang ingin ditambahkan.
            </p>

            <!-- Form tambah buku -->
            <form
                @submit.prevent="submit"
                class="mt-4 max-w-xl space-y-4"
            >

                <!-- Kode Buku -->
                <div>
                    <label class="mb-1 block font-medium">
                        Kode Buku
                    </label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                        placeholder="Masukkan kode buku"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.kode_buku" class="mt-1 text-sm text-red-600">
                        {{ form.errors.kode_buku }}
                    </div>
                </div>

                <!-- Judul -->
                <div>
                    <label class="mb-1 block font-medium">
                        Judul
                    </label>

                    <input
                        v-model="form.judul"
                        type="text"
                        placeholder="Masukkan judul buku"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.judul" class="mt-1 text-sm text-red-600">
                        {{ form.errors.judul }}
                    </div>
                </div>

                <!-- Pengarang -->
                <div>
                    <label class="mb-1 block font-medium">
                        Pengarang
                    </label>

                    <input
                        v-model="form.pengarang"
                        type="text"
                        placeholder="Masukkan nama pengarang"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.pengarang" class="mt-1 text-sm text-red-600">
                        {{ form.errors.pengarang }}
                    </div>
                </div>

                <!-- Penerbit -->
                <div>
                    <label class="mb-1 block font-medium">
                        Penerbit
                    </label>

                    <input
                        v-model="form.penerbit"
                        type="text"
                        placeholder="Masukkan nama penerbit"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.penerbit" class="mt-1 text-sm text-red-600">
                        {{ form.errors.penerbit }}
                    </div>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="mb-1 block font-medium">
                        Tahun
                    </label>

                    <input
                        v-model="form.tahun"
                        type="number"
                        placeholder="Contoh: 2024"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.tahun" class="mt-1 text-sm text-red-600">
                        {{ form.errors.tahun }}
                    </div>
                </div>

                <!-- Stok -->
                <div>
                    <label class="mb-1 block font-medium">
                        Stok
                    </label>

                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="w-full rounded-lg border px-3 py-2"
                    >

                    <div v-if="form.errors.stok" class="mt-1 text-sm text-red-600">
                        {{ form.errors.stok }}
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex items-center gap-3">

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>

                    <Link
                        href="/books"
                        class="rounded-lg bg-gray-500 px-5 py-2 text-white hover:bg-gray-600"
                    >
                        Kembali
                    </Link>

                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
