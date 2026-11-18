
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Chart.js untuk membuat grafik
import Chart from 'chart.js/auto';

// Data dari komponen induk
const props = defineProps({
    label: Array,
    jumlah: Array,
    judul: {
        type: String,
        default: 'Jumlah',
    },
});

// Menghubungkan elemen canvas
const kanvas = ref(null);

// Menyimpan objek grafik
let grafik = null;

// Membuat grafik setelah halaman ditampilkan
onMounted(() => {
    grafik = new Chart(kanvas.value, {
        type: 'bar',

        data: {
            labels: [...(props.label || [])],

            datasets: [
                {
                    label: props.judul,
                    data: [...(props.jumlah || [])],
                    backgroundColor: '#3b82f6',
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
});

// Menghapus grafik ketika komponen dilepas
onBeforeUnmount(() => {
    if (grafik) {
        grafik.destroy();
    }
});
</script>

<template>
    <div class="h-64">
        <canvas ref="kanvas"></canvas>
    </div>
</template>
