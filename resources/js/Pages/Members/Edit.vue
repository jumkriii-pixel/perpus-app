<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

// Data anggota dari MemberController
const props = defineProps({
    member: Object,
});

// Form edit anggota
const form = useForm({
    _method: 'put',
    nama: props.member.nama,
    nis_nim: props.member.nis_nim,
    no_hp: props.member.no_hp,
    foto: null,
});

// Pratinjau foto baru
const pratinjau = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files[0];

    form.foto = berkas ?? null;

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }

    pratinjau.value = berkas
        ? URL.createObjectURL(berkas)
        : null;
}

// Validasi realtime NIS/NIM
const statusNis = ref('');

let timerNis = null;
let permintaanNis = null;

async function periksaNis() {
    if (permintaanNis) {
        permintaanNis.abort();
    }

    const controller = new AbortController();
    permintaanNis = controller;

    statusNis.value = 'memeriksa';

    try {
        const respons = await axios.get(route('members.cek-nis'), {
            params: {
                nis_nim: form.nis_nim.trim(),
                abaikan: props.member.id,
            },
            signal: controller.signal,
        });

        if (permintaanNis !== controller) return;

        statusNis.value = respons.data.tersedia
            ? 'tersedia'
            : 'terpakai';

    } catch (error) {
        if (
            error.code === 'ERR_CANCELED' ||
            axios.isCancel(error)
        ) {
            return;
        }

        if (permintaanNis === controller) {
            statusNis.value = 'gagal';
        }
    }
}

// Pantau perubahan NIS/NIM
watch(() => form.nis_nim, (nilai) => {
    clearTimeout(timerNis);

    if (permintaanNis) {
        permintaanNis.abort();
        permintaanNis = null;
    }

    // Jika kosong, hapus status pemeriksaan
    if (!nilai || nilai.trim() === '') {
        statusNis.value = '';
        return;
    }

    statusNis.value = '';

    // Tunggu 500 milidetik setelah pengguna selesai mengetik
    timerNis = setTimeout(() => {
        periksaNis();
    }, 500);
});

// Bersihkan timer dan permintaan saat halaman ditinggalkan
onBeforeUnmount(() => {
    clearTimeout(timerNis);

    if (permintaanNis) {
        permintaanNis.abort();
    }

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }
});

// Simpan perubahan anggota
function submit() {
    form.post(route('members.update', props.member.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Edit Anggota</h1>

            <form @submit.prevent="submit" class="mt-3 space-y-3">

                <!-- Nama -->
                <div>
                    <label class="block">Nama</label>

                    <input
                        v-model="form.nama"
                        type="text"
                        class="w-full rounded border p-2"
                    >

                    <div v-if="form.errors.nama" class="text-red-600">
                        {{ form.errors.nama }}
                    </div>
                </div>

                <!-- NIS/NIM -->
                <div>
                    <label class="block">NIS/NIM</label>

                    <input
                        v-model="form.nis_nim"
                        type="text"
                        class="w-full rounded border p-2"
                    >

                    <div
                        v-if="statusNis === 'memeriksa'"
                        class="text-gray-500"
                    >
                        Memeriksa NIS/NIM...
                    </div>

                    <div
                        v-else-if="statusNis === 'tersedia'"
                        class="text-green-700"
                    >
                        NIS/NIM ini tersedia.
                    </div>

                    <div
                        v-else-if="statusNis === 'terpakai'"
                        class="text-red-600"
                    >
                        NIS/NIM ini sudah terdaftar. Gunakan nomor lain.
                    </div>

                    <div
                        v-else-if="statusNis === 'gagal'"
                        class="text-yellow-700"
                    >
                        Pemeriksaan otomatis gagal. Server akan memeriksa ulang saat disimpan.
                    </div>

                    <div v-if="form.errors.nis_nim" class="text-red-600">
                        {{ form.errors.nis_nim }}
                    </div>
                </div>

                <!-- Nomor HP -->
                <div>
                    <label class="block">No. HP</label>

                    <input
                        v-model="form.no_hp"
                        type="text"
                        class="w-full rounded border p-2"
                    >

                    <div v-if="form.errors.no_hp" class="text-red-600">
                        {{ form.errors.no_hp }}
                    </div>
                </div>

                <!-- Foto saat ini -->
                <div>
                    <label class="block">Foto Saat Ini</label>

                    <img
                        v-if="member.foto"
                        :src="`/storage/${member.foto}`"
                        alt="Foto saat ini"
                        class="h-32 w-32 rounded object-cover"
                    >

                    <span v-else>
                        Belum ada foto.
                    </span>
                </div>

                <!-- Ganti foto -->
                <div>
                    <label class="block">
                        Ganti Foto (kosongkan jika tidak ingin mengganti)
                    </label>

                    <input
                        type="file"
                        accept="image/png, image/jpeg"
                        @change="pilihFoto"
                    >

                    <div v-if="form.errors.foto" class="text-red-600">
                        {{ form.errors.foto }}
                    </div>

                    <img
                        v-if="pratinjau"
                        :src="pratinjau"
                        alt="Pratinjau foto baru"
                        class="mt-2 h-32 w-32 rounded object-cover"
                    >
                </div>

                <!-- Tombol simpan -->
                <button
                    type="submit"
                    :disabled="
                        form.processing ||
                        statusNis === 'terpakai' ||
                        statusNis === 'memeriksa'
                    "
                    class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
