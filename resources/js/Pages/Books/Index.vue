<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    books: Object,
    search: String,
});

const keyword = ref(props.search ?? '');
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

let timer = null;
let permintaan = null;

const daftarBuku = computed(() => {
    return hasilCari.value ?? props.books.data;
});

// Mengambil hasil pencarian melalui Axios
async function ambilHasil() {
    if (permintaan) {
        permintaan.abort();
    }

    const controller = new AbortController();
    permintaan = controller;

    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('books.search'), {
            params: {
                q: keyword.value.trim()
            },
            signal: controller.signal,
        });

        // Mencegah respons lama menimpa hasil pencarian terbaru
        if (permintaan === controller) {
            hasilCari.value = respons.data;
            sedangMencari.value = false;
        }
    } catch (error) {
        if (error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)) {
            return;
        }

        if (permintaan === controller) {
            sedangMencari.value = false;
            pesanError.value =
                'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
        }
    }
}

// Pencarian otomatis setiap kali pengguna mengetik
watch(keyword, (nilai) => {
    clearTimeout(timer);

    if (nilai.trim() === '') {
        if (permintaan) {
            permintaan.abort();
            permintaan = null;
        }

        hasilCari.value = null;
        sedangMencari.value = false;
        pesanError.value = '';
        return;
    }

    timer = setTimeout(ambilHasil, 300);
});

// Menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id), {
            onSuccess: () => {
                if (keyword.value.trim() !== '') {
                    ambilHasil();
                }
            },
        });
    }
}

// Membersihkan timer dan permintaan ketika halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timer);

    if (permintaan) {
        permintaan.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="container-fluid">
            <h1>Data Buku</h1>

            <!-- Pesan sukses -->
            <div
                v-if="$page.props.flash?.success"
                class="alert alert-success"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah buku -->
            <div class="mb-3">
                <Link
                    href="/books/create"
                    class="btn btn-primary"
                >
                    + Tambah Buku
                </Link>
            </div>

            <!-- Kotak pencarian -->
            <div class="mb-3">
                <input
                    v-model="keyword"
                    type="text"
                    class="form-control"
                    placeholder="Ketik judul atau pengarang..."
                >

                <small v-if="sedangMencari" class="text-muted">
                    Mencari...
                </small>

                <p v-if="pesanError" class="text-danger mt-2">
                    {{ pesanError }}
                </p>
            </div>

            <!-- Tabel buku -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in daftarBuku"
                            :key="book.id"
                        >
                            <td>{{ book.kode_buku }}</td>
                            <td>{{ book.judul }}</td>
                            <td>{{ book.pengarang }}</td>
                            <td>{{ book.stok }}</td>

                            <td>
                                <Link
                                    :href="`/books/${book.id}/edit`"
                                    class="btn btn-warning btn-sm"
                                >
                                    Edit
                                </Link>

                                <button
                                    @click="hapus(book.id)"
                                    class="btn btn-danger btn-sm ms-1"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>

                        <tr v-if="daftarBuku.length === 0">
                            <td colspan="5" class="text-center">
                                Tidak ada buku yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
