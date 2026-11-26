/**
 * DigiLib - Pustakawan Dashboard Scripts
 * Operasional Sirkulasi Publik, Chart.js Visualizations with Smooth Animations
 */

document.addEventListener('DOMContentLoaded', () => {
  // Chart 1: Tren Peminjaman & Pengembalian Minggu Ini (Bar Chart)
  const ctxWeeklyLoans = document.getElementById('chartWeeklyLoans');
  if (ctxWeeklyLoans) {
    const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    const pinjamData = [18, 24, 22, 35, 28, 42, 26];
    const kembaliData = [14, 19, 25, 20, 31, 38, 22];

    const chartWeekly = new Chart(ctxWeeklyLoans, {
      type: 'bar',
      data: {
        labels: days,
        datasets: [
          {
            label: 'Buku Dipinjam',
            data: [0, 0, 0, 0, 0, 0, 0],
            backgroundColor: '#1d4ed8',
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          },
          {
            label: 'Buku Dikembalikan',
            data: [0, 0, 0, 0, 0, 0, 0],
            backgroundColor: '#10b981',
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1200,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 12,
            cornerRadius: 10,
            callbacks: {
              label: (context) => ` ${context.dataset.label}: ${context.raw} buku`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
              color: '#64748b'
            }
          },
          y: {
            grid: {
              color: '#e2e8f0',
              drawBorder: false
            },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 11 },
              color: '#94a3b8',
              stepSize: 10
            },
            beginAtZero: true
          }
        }
      }
    });

    // Trigger animation smoothly
    setTimeout(() => {
      chartWeekly.data.datasets[0].data = pinjamData;
      chartWeekly.data.datasets[1].data = kembaliData;
      chartWeekly.update();
    }, 120);
  }

  // Chart 2: Komposisi Kategori Koleksi Buku (Doughnut Chart)
  const ctxCategory = document.getElementById('chartBookCategory');
  if (ctxCategory) {
    const categories = ['Fiksi & Sastra', 'Sains & Teknologi', 'Sejarah & Budaya', 'Anak & Remaja', 'Pengembangan Diri'];
    const categoryTotals = [2850, 1920, 1420, 1260, 1000];

    const chartCat = new Chart(ctxCategory, {
      type: 'doughnut',
      data: {
        labels: categories,
        datasets: [{
          data: [0, 0, 0, 0, 0],
          backgroundColor: [
            '#1d4ed8',
            '#0284c7',
            '#059669',
            '#d97706',
            '#7c3aed'
          ],
          borderWidth: 3,
          borderColor: '#e9eff8',
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          animateRotate: true,
          animateScale: true,
          duration: 1300,
          easing: 'easeOutCubic'
        },
        cutout: '72%',
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 10,
            cornerRadius: 10,
            callbacks: {
              label: (context) => ` ${context.label}: ${context.raw} buku`
            }
          }
        }
      }
    });

    setTimeout(() => {
      chartCat.data.datasets[0].data = categoryTotals;
      chartCat.update();
    }, 150);
  }
});
