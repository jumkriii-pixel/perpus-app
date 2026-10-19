
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    books: Object,
    search: {
        type: String,
        default: '',
    },
});

const keyword = ref(props.search || '');

// Fungsi pencarian buku
function cari() {
    router.get('/books', {
        search: keyword.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Fungsi hapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Data Buku
                </h1>

                <Link
                    href="/books/create"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow"
                >
                    + Tambah Buku
                </Link>
            </div>

            <!-- Pesan sukses -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Kotak Input Pencarian -->
            <div class="bg-white shadow-md rounded-lg p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">
                    Cari Data Buku
                </h2>

                <form
                    @submit.prevent="cari"
                    class="flex flex-col sm:flex-row gap-3"
                >
                    <input
                        v-model="keyword"
                        type="text"
                        placeholder="Masukkan kode atau judul buku..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg"
                    >
                        Cari
                    </button>

                    <Link
                        v-if="keyword"
                        href="/books"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-lg text-center"
                    >
                        Reset
                    </Link>
                </form>
            </div>

            <!-- Tabel Buku -->
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Kode</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Judul</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Pengarang</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Penerbit</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Tahun</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Stok</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in (props.books?.data || [])"
                            :key="book.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ book.kode_buku }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm font-medium text-gray-900">
                                {{ book.judul }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ book.pengarang }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ book.penerbit || '-' }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ book.tahun || '-' }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ book.stok }}
                            </td>

                            <td class="px-5 py-4 border-b border-gray-200 text-sm text-center whitespace-nowrap">
                                <Link
                                    :href="route('books.edit', book.id)"
                                    class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs font-semibold mr-1"
                                >
                                    Edit
                                </Link>

                                <button
                                    @click="hapus(book.id)"
                                    type="button"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>

                        <tr v-if="!props.books?.data || props.books.data.length === 0">
                            <td colspan="7" class="px-5 py-5 text-center text-gray-500 text-sm">
                                {{ keyword ? 'Buku tidak ditemukan.' : 'Belum ada data buku.' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Informasi jumlah data -->
            <div
                v-if="props.books?.total !== undefined"
                class="mt-4 text-sm text-gray-600"
            >
                Total buku: {{ props.books.total }}
            </div>

        </div>
    </AuthenticatedLayout>
</template>
