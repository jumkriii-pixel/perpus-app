<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    books: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Data Buku</h1>

            <Link
                href="/books/create"
                class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700"
            >
                + Tambah Buku
            </Link>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3">Kode</th>
                            <th class="border p-3">Judul</th>
                            <th class="border p-3">Pengarang</th>
                            <!-- Kolom Baru: Tahun -->
                            <th class="border p-3">Tahun</th>
                            <th class="border p-3">Stok</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in books.data"
                            :key="book.id"
                        >
                            <td class="border p-3">{{ book.kode_buku }}</td>
                            <td class="border p-3">{{ book.judul }}</td>
                            <td class="border p-3">{{ book.pengarang }}</td>
                            <!-- Data Kolom Baru: Tahun -->
                            <td class="border p-3">{{ book.tahun || '-' }}</td>
                            <td class="border p-3">{{ book.stok }}</td>
                        </tr>

                        <tr v-if="!books.data || books.data.length === 0">
                            <!-- colspan diubah menjadi 5 karena jumlah kolom tabel bertambah menjadi 5 -->
                            <td colspan="5" class="border p-3 text-center">
                                Belum ada data buku.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>