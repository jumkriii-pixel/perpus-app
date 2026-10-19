<script setup> 
import { Link } from '@inertiajs/vue3'; 
</script> 

<template> 
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex-shrink-0"> 
        <h2 class="text-lg font-bold mb-4">Perpustakaan Mini</h2> 
        
        <nav class="space-y-2"> 
            <Link href="/dashboard" class="block py-2 px-3 rounded hover:bg-gray-700 hover:text-white transition">
                Dashboard
            </Link> 
            <Link href="/" class="block py-2 px-3 rounded hover:bg-gray-700 hover:text-white transition">
                Beranda
            </Link> 
            <!-- Menu menuju daftar buku (sesuai dengan Route::resource) --> 
            <Link href="/books" class="block py-2 px-3 rounded hover:bg-gray-700 hover:text-white transition">
                Data Buku
            </Link> 
        </nav> 
    </aside> 
</template>