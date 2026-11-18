<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    loans: Object,
})
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Peminjaman</h1>

            <p class="mt-1 text-gray-600">
                Kelola data peminjaman buku anggota perpustakaan.
            </p>

            <!-- Pesan berhasil -->
            <div
                v-if="$page.props.flash?.success"
                class="my-2 rounded bg-green-100 p-3 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Tombol tambah -->
            <div class="my-3">
                <Link
                    href="/loans/create"
                    class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    + Pinjam Buku
                </Link>
            </div>

            <!-- Tabel peminjaman -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Anggota</th>
                            <th class="border px-3 py-2">Kode Buku</th>
                            <th class="border px-3 py-2">Buku</th>
                            <th class="border px-3 py-2">Tanggal Pinjam</th>
                            <th class="border px-3 py-2">Jatuh Tempo</th>
                            <th class="border px-3 py-2">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="loan in loans.data"
                            :key="loan.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border px-3 py-2">
                                {{ loan.member?.nama ?? '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.book?.kode_buku ?? '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.book?.judul ?? '-' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.tanggal_pinjam }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ loan.tanggal_kembali_rencana }}
                            </td>

                            <td class="border px-3 py-2">
                                <span
                                    :class="loan.status === 'dipinjam'
                                        ? 'rounded bg-yellow-100 px-2 py-1 text-yellow-700'
                                        : 'rounded bg-green-100 px-2 py-1 text-green-700'"
                                >
                                    {{ loan.status }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="loans.data.length === 0">
                            <td
                                colspan="6"
                                class="border px-3 py-4 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex flex-wrap gap-2">
                <template v-for="link in loans.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2 hover:bg-gray-100"
                        :class="{ 'bg-blue-600 text-white': link.active }"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="rounded border px-3 py-2 text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
