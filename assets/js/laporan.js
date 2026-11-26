/**
 * DigiLib - Laporan & Analitik Charts
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Tren Sirkulasi & Peminjaman Bar Chart
  const circulationCanvas = document.getElementById('reportCirculationChart');
  if (circulationCanvas) {
    const ctx = circulationCanvas.getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
        datasets: [
          {
            label: 'Buku Fisik',
            data: [620, 820, 1100, 950, 1150, 780, 710, 1050, 1420, 1840],
            backgroundColor: '#2563eb',
            borderRadius: 4,
            barPercentage: 0.5,
            categoryPercentage: 0.8
          },
          {
            label: 'E-Book & Jurnal Digital',
            data: [410, 520, 780, 690, 890, 480, 420, 810, 1120, 1380],
            backgroundColor: '#0d9488',
            borderRadius: 4,
            barPercentage: 0.5,
            categoryPercentage: 0.8
          },
          {
            label: 'Pengembalian Tepat Waktu',
            data: [580, 790, 1050, 910, 1100, 740, 680, 1010, 1370, 1790],
            backgroundColor: '#10b981',
            borderRadius: 4,
            barPercentage: 0.5,
            categoryPercentage: 0.8
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.9)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 10,
            cornerRadius: 8
          }
        },
        scales: {
          x: {
            grid: { display: false, drawBorder: false },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
              color: '#64748b'
            }
          },
          y: {
            grid: {
              color: 'rgba(166, 180, 200, 0.2)',
              drawBorder: false
            },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 10, weight: '500' },
              color: '#94a3b8'
            }
          }
        }
      }
    });
  }

  // 2. Distribusi Civitas Donut Chart
  const civitasCanvas = document.getElementById('civitasDonutChart');
  if (civitasCanvas) {
    const ctxDonut = civitasCanvas.getContext('2d');
    new Chart(ctxDonut, {
      type: 'doughnut',
      data: {
        labels: ['Mahasiswa S1', 'Dosen & Peneliti', 'Pascasarjana (S2/S3)', 'Tendik & Mitra Luar'],
        datasets: [
          {
            data: [64, 18, 12, 6],
            backgroundColor: ['#2563eb', '#0d9488', '#10b981', '#f59e0b'],
            borderWidth: 0,
            hoverOffset: 4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.9)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 8,
            cornerRadius: 8
          }
        }
      }
    });
  }
});
