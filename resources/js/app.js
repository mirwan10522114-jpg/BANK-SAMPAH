import Chart from 'chart.js/auto';

// Membuat Chart.js bisa diakses secara global di semua file Blade,
// supaya bisa dipakai langsung lewat `new Chart(...)` tanpa import ulang.
window.Chart = Chart;