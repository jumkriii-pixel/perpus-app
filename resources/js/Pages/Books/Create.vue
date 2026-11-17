<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

// Objek form
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Status validasi kode buku
const statusKode = ref('');

let timerKode = null;
let permintaanKode = null;

// Memeriksa kode buku ke database
async function periksaKode() {
    if (permintaanKode) {
        permintaanKode.abort();
    }

    const controller = new AbortController();
    permintaanKode = controller;

    statusKode.value = 'memeriksa';

    try {
        const respons = await axios.get(route('books.cek-kode'), {
            params: {
                kode: form.kode_buku.trim(),
            },
            signal: controller.signal,
        });

        if (permintaanKode !== controller) return;

        statusKode.value = respons.data.tersedia
            ? 'tersedia'
            : 'terpakai';

    } catch (error) {
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return;
        }

        if (permintaanKode === controller) {
            statusKode.value = 'gagal';
        }
    }
}

// Memantau perubahan input kode buku
watch(() => form.kode_buku, (nilai) => {
    clearTimeout(timerKode);

    if (permintaanKode) {
        permintaanKode.abort();
        permintaanKode = null;
    }

    if (!nilai || nilai.trim() === '') {
        statusKode.value = '';
        return;
    }

    statusKode.value = '';

    timerKode = setTimeout(() => {
        periksaKode();
    }, 500);
});

// Membersihkan timer ketika halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timerKode);

    if (permintaanKode) {
        permintaanKode.abort();
    }
});

// Menyimpan data buku
function submit() {
    if (
        statusKode.value === 'terpakai' ||
        statusKode.value === 'memeriksa'
    ) {
        return;
    }

    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">

            <h1 class="text-2xl font-bold mb-4">
                Tambah Buku
            </h1>

            <form @submit.prevent="submit" class="space-y-4">

                <!-- Kode Buku -->
                <div>
                    <label class="block font-medium mb-1">
                        Kode Buku
                    </label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        placeholder="Masukkan kode buku"
                        required
                    >

                    <!-- Pesan validasi realtime -->
                    <p
                        v-if="statusKode === 'memeriksa'"
                        class="text-gray-500 text-sm mt-1"
                    >
                        Memeriksa kode buku...
                    </p>

                    <p
                        v-else-if="statusKode === 'tersedia'"
                        class="text-green-600 text-sm mt-1"
                    >
                        ✓ Kode buku tersedia.
                    </p>

                    <p
                        v-else-if="statusKode === 'terpakai'"
                        class="text-red-600 text-sm mt-1"
                    >
                        ✗ Kode buku sudah terdaftar. Gunakan kode lain.
                    </p>

                    <p
                        v-else-if="statusKode === 'gagal'"
                        class="text-yellow-600 text-sm mt-1"
                    >
                        Pemeriksaan gagal. Silakan coba lagi.
                    </p>

                    <span
                        v-if="form.errors.kode_buku"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.kode_buku }}
                    </span>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block font-medium mb-1">
                        Judul
                    </label>

                    <input
                        v-model="form.judul"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        required
                    >

                    <span
                        v-if="form.errors.judul"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.judul }}
                    </span>
                </div>

                <!-- Pengarang -->
                <div>
                    <label class="block font-medium mb-1">
                        Pengarang
                    </label>

                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        required
                    >

                    <span
                        v-if="form.errors.pengarang"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.pengarang }}
                    </span>
                </div>

                <!-- Penerbit -->
                <div>
                    <label class="block font-medium mb-1">
                        Penerbit
                    </label>

                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                    >

                    <span
                        v-if="form.errors.penerbit"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.penerbit }}
                    </span>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block font-medium mb-1">
                        Tahun
                    </label>

                    <input
                        v-model="form.tahun"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        maxlength="4"
                    >

                    <span
                        v-if="form.errors.tahun"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.tahun }}
                    </span>
                </div>

                <!-- Stok -->
                <div>
                    <label class="block font-medium mb-1">
                        Stok
                    </label>

                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="w-full border rounded px-3 py-2"
                        required
                    >

                    <span
                        v-if="form.errors.stok"
                        class="text-red-500 text-sm"
                    >
                        {{ form.errors.stok }}
                    </span>
                </div>

                <!-- Tombol Simpan -->
                <button
                    type="submit"
                    :disabled="
                        form.processing ||
                        statusKode === 'terpakai' ||
                        statusKode === 'memeriksa'
                    "
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
