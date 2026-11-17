<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    members: Object,
    search: String,
});

// Kata kunci pencarian
const keyword = ref(props.search ?? '');

// Menyimpan hasil pencarian dari server
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

// Timer debounce dan pengendali permintaan Axios
let timer = null;
let permintaan = null;

// Data yang ditampilkan
const daftarAnggota = computed(() =>
    hasilCari.value ?? props.members.data
);

// Fungsi live search menggunakan Axios
async function ambilHasil() {
    // Batalkan permintaan sebelumnya
    if (permintaan) {
        permintaan.abort();
    }

    const controller = new AbortController();
    permintaan = controller;

    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('members.search'), {
            params: {
                q: keyword.value.trim()
            },
            signal: controller.signal,
        });

        // Pastikan respons berasal dari permintaan terbaru
        if (permintaan === controller) {
            hasilCari.value = respons.data;
            sedangMencari.value = false;
        }

    } catch (error) {
        // Abaikan permintaan yang sengaja dibatalkan
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return;
        }

        if (permintaan === controller) {
            sedangMencari.value = false;
            pesanError.value =
                'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
        }
    }
}

// Debounce: menunggu 300 milidetik setelah pengguna berhenti mengetik
watch(keyword, (nilai) => {
    clearTimeout(timer);

    // Jika kotak pencarian kosong, tampilkan daftar bawaan
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

    // Kirim pencarian setelah 300 milidetik
    timer = setTimeout(() => {
        ambilHasil();
    }, 300);
});

// Fungsi menghapus anggota
function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id), {
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
        <div class="p-4">

            <h1 class="text-xl font-bold">
                Data Anggota
            </h1>

            <!-- Notifikasi berhasil -->
            <div
                v-if="$page.props.flash?.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah dan kotak pencarian -->
            <div class="my-3 flex flex-wrap items-center gap-3">

                <Link
                    href="/members/create"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Tambah Anggota
                </Link>

                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik nama atau NIS/NIM..."
                    class="rounded border px-3 py-2"
                >

                <span
                    v-if="sedangMencari"
                    class="text-gray-500"
                >
                    Mencari...
                </span>

            </div>

            <!-- Pesan kesalahan pencarian -->
            <div
                v-if="pesanError"
                class="my-2 text-red-600"
            >
                {{ pesanError }}
            </div>

            <!-- Tabel data anggota -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">

                    <thead>
                        <tr>
                            <th class="border px-3 py-2">Foto</th>
                            <th class="border px-3 py-2">NIS/NIM</th>
                            <th class="border px-3 py-2">Nama</th>
                            <th class="border px-3 py-2">No. HP</th>
                            <th class="border px-3 py-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr
                            v-for="member in daftarAnggota"
                            :key="member.id"
                        >

                            <!-- Foto anggota -->
                            <td class="border px-3 py-2">
                                <img
                                    v-if="member.foto"
                                    :src="`/storage/${member.foto}`"
                                    alt="Foto anggota"
                                    class="h-16 w-16 object-cover"
                                >

                                <span v-else>-</span>
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.nis_nim }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.nama }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ member.no_hp }}
                            </td>

                            <!-- Tombol aksi -->
                            <td class="border px-3 py-2">

                                <Link
                                    :href="`/members/${member.id}/edit`"
                                    class="mr-3 text-blue-600"
                                >
                                    Edit
                                </Link>

                                <button
                                    @click="hapus(member.id)"
                                    class="text-red-600"
                                >
                                    Hapus
                                </button>

                            </td>
                        </tr>

                        <!-- Jika data kosong -->
                        <tr v-if="daftarAnggota.length === 0">
                            <td
                                colspan="5"
                                class="border px-3 py-2 text-center"
                            >
                                {{
                                    keyword.trim() !== ''
                                        ? 'Tidak ada anggota yang ditemukan.'
                                        : 'Belum ada data anggota.'
                                }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Pagination daftar anggota bawaan -->
            <div
                v-if="hasilCari === null"
                class="mt-3 flex flex-wrap gap-3"
            >
                <template
                    v-for="(link, index) in members.links"
                    :key="index"
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
