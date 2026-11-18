<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    books: Object,
    filters: Object,
})

const keyword = ref(props.filters?.search || '')
const daftarBuku = ref(props.books.data)
const hasilCari = ref(null)
const sedangMencari = ref(false)
const pesanError = ref('')

// Pencarian otomatis ketika keyword berubah
let timer = null

watch(keyword, (nilai) => {
    clearTimeout(timer)

    timer = setTimeout(() => {
        if (nilai.trim() === '') {
            daftarBuku.value = props.books.data
            hasilCari.value = null
            pesanError.value = ''
            return
        }

        sedangMencari.value = true
        pesanError.value = ''

        router.get('/books', {
            search: nilai
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['books', 'filters'],
            onSuccess: (page) => {
                daftarBuku.value = page.props.books.data
                hasilCari.value = page.props.books.data
            },
            onError: () => {
                pesanError.value = 'Terjadi kesalahan saat mencari buku.'
            },
            onFinish: () => {
                sedangMencari.value = false
            }
        })
    }, 400)
})

// Menghapus buku
function hapus(id) {
    if (confirm('Apakah kamu yakin ingin menghapus buku ini?')) {
        router.delete(`/books/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="text-xl font-bold">Data Buku</h1>

            <!-- Flash message -->
            <div
                v-if="$page.props.flash.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah dan pencarian -->
            <div class="my-3 flex items-center gap-3">

                <Link
                    href="/books/create"
                    class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    + Tambah Buku
                </Link>

                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik judul atau pengarang..."
                    class="rounded border px-3 py-2"
                >

                <span
                    v-if="sedangMencari"
                    class="text-gray-500"
                >
                    Mencari...
                </span>

            </div>

            <!-- Pesan error -->
            <div v-if="pesanError" class="my-2 text-red-600">
                {{ pesanError }}
            </div>

            <!-- Tabel buku -->
            <table class="border-collapse">

                <thead>
                    <tr>
                        <th class="border px-3 py-2">Kode</th>
                        <th class="border px-3 py-2">Judul</th>
                        <th class="border px-3 py-2">Pengarang</th>
                        <th class="border px-3 py-2">Stok</th>
                        <th class="border px-3 py-2">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <tr
                        v-for="book in daftarBuku"
                        :key="book.id"
                    >
                        <td class="border px-3 py-2">
                            {{ book.kode_buku }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.judul }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.pengarang }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ book.stok }}
                        </td>

                        <td class="border px-3 py-2">

                            <Link
                                :href="`/books/${book.id}/edit`"
                                class="mr-3 text-blue-600 hover:underline"
                            >
                                Edit
                            </Link>

                            <button
                                @click="hapus(book.id)"
                                class="text-red-600 hover:underline"
                            >
                                Hapus
                            </button>

                        </td>
                    </tr>

                    <!-- Jika data kosong -->
                    <tr v-if="daftarBuku.length === 0">
                        <td colspan="5" class="border px-3 py-2 text-center">
                            Tidak ada buku yang ditemukan.
                        </td>
                    </tr>

                </tbody>
            </table>

            <!-- Pagination -->
            <div
                v-if="hasilCari === null"
                class="mt-3 flex gap-3"
            >
                <template
                    v-for="link in books.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="text-gray-400"
                    />
                </template>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
