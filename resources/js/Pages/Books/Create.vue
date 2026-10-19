<script setup> 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; 
import { useForm } from '@inertiajs/vue3'; 

// Objek form untuk mengelola nilai input dan pesan error
const form = useForm({ 
    kode_buku: '', 
    judul: '', 
    pengarang: '', 
    penerbit: '', 
    tahun: '', 
    stok: 0, 
}); 

// Dipanggil ketika tombol submit diklik 
function submit() { 
    form.post(route('books.store')); 
} 
</script> 

<template> 
    <AuthenticatedLayout> 
        <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
            <h1 class="text-2xl font-bold mb-4">Tambah Buku</h1> 

            <form @submit.prevent="submit" class="space-y-4"> 
                <!-- Kode Buku -->
                <div>
                    <label class="block font-medium">Kode Buku</label> 
                    <input v-model="form.kode_buku" type="text" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.kode_buku" class="text-red-500 text-sm">{{ form.errors.kode_buku }}</span> 
                </div>

                <!-- Judul -->
                <div>
                    <label class="block font-medium">Judul</label> 
                    <input v-model="form.judul" type="text" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.judul" class="text-red-500 text-sm">{{ form.errors.judul }}</span> 
                </div>

                <!-- Pengarang -->
                <div>
                    <label class="block font-medium">Pengarang</label> 
                    <input v-model="form.pengarang" type="text" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.pengarang" class="text-red-500 text-sm">{{ form.errors.pengarang }}</span> 
                </div>

                <!-- Penerbit -->
                <div>
                    <label class="block font-medium">Penerbit</label> 
                    <input v-model="form.penerbit" type="text" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.penerbit" class="text-red-500 text-sm">{{ form.errors.penerbit }}</span> 
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block font-medium">Tahun</label> 
                    <input v-model="form.tahun" type="text" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.tahun" class="text-red-500 text-sm">{{ form.errors.tahun }}</span> 
                </div>

                <!-- Stok -->
                <div>
                    <label class="block font-medium">Stok</label> 
                    <input v-model="form.stok" type="number" class="w-full border rounded px-3 py-2"> 
                    <span v-if="form.errors.stok" class="text-red-500 text-sm">{{ form.errors.stok }}</span> 
                </div>

                <!-- Tombol Simpan -->
                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button> 
            </form> 
        </div>
    </AuthenticatedLayout> 
</template>