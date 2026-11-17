
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null,
});

function simpan() {
    form.post(route('members.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-5">

            <h1 class="mb-5 text-2xl font-bold">
                Tambah Anggota
            </h1>

            <form @submit.prevent="simpan">

                <!-- Nama -->
                <div class="mb-4">
                    <label class="block mb-1">Nama</label>
                    <input
                        v-model="form.nama"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div v-if="form.errors.nama" class="text-red-600">
                        {{ form.errors.nama }}
                    </div>
                </div>

                <!-- NIS/NIM -->
                <div class="mb-4">
                    <label class="block mb-1">NIS/NIM</label>
                    <input
                        v-model="form.nis_nim"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div v-if="form.errors.nis_nim" class="text-red-600">
                        {{ form.errors.nis_nim }}
                    </div>
                </div>

                <!-- No HP -->
                <div class="mb-4">
                    <label class="block mb-1">No. HP</label>
                    <input
                        v-model="form.no_hp"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                    >
                    <div v-if="form.errors.no_hp" class="text-red-600">
                        {{ form.errors.no_hp }}
                    </div>
                </div>

                <!-- Foto -->
                <div class="mb-4">
                    <label class="block mb-1">
                        Foto (JPG/PNG, maksimal 2 MB)
                    </label>

                    <input
                        type="file"
                        accept="image/jpeg,image/png"
                        @input="form.foto = $event.target.files[0]"
                    >

                    <div v-if="form.errors.foto" class="text-red-600">
                        {{ form.errors.foto }}
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex gap-3">

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-blue-600 px-5 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>

                    <Link
                        href="/members"
                        class="rounded bg-gray-500 px-5 py-2 text-white"
                    >
                        Kembali
                    </Link>

                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
