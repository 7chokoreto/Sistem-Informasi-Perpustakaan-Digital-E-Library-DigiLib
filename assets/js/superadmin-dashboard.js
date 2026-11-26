/**
 * DigiLib - Superadmin Dashboard Analytics & Charts (General Library System)
 * Account Growth & Account Status Health Analytics with Guaranteed Entrance Animations
 */

document.addEventListener('DOMContentLoaded', () => {
  // Chart 1: Tren Pendaftaran Akun Baru (Member & Pustakawan)
  const growthChartCanvas = document.getElementById('accountGrowthChart');
  if (growthChartCanvas) {
    const ctx = growthChartCanvas.getContext('2d');

    const datasetsByPeriod = {
      Bulanan: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        member: [140, 180, 220, 195, 240, 210, 185, 310, 420, 490, 310, 210],
        pustakawan: [1, 0, 1, 0, 2, 0, 0, 2, 1, 1, 0, 1]
      },
      Mingguan: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        member: [85, 110, 130, 145],
        pustakawan: [0, 1, 0, 0]
      },
      Tahunan: {
        labels: ['2021', '2022', '2023', '2024'],
        member: [1200, 1950, 2700, 3412],
        pustakawan: [8, 11, 14, 16]
      }
    };

    const emptyArray = new Array(datasetsByPeriod.Bulanan.labels.length).fill(0);

    const growthChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: datasetsByPeriod.Bulanan.labels,
        datasets: [
          {
            label: 'Member Umum Baru',
            data: emptyArray,
            backgroundColor: '#2563eb',
            borderRadius: 6,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          },
          {
            label: 'Pustakawan Baru',
            data: emptyArray,
            backgroundColor: '#0f8a5f',
            borderRadius: 6,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1600,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 12,
            cornerRadius: 10
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
            beginAtZero: true,
            suggestedMax: 550,
            grid: {
              color: 'rgba(166, 180, 200, 0.25)',
              drawBorder: false
            },
            ticks: {
              font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
              color: '#94a3b8'
            }
          }
        }
      }
    });

    // Trigger Entrance Growth Animation with slight microtask delay
    setTimeout(() => {
      growthChart.data.datasets[0].data = datasetsByPeriod.Bulanan.member;
      growthChart.data.datasets[1].data = datasetsByPeriod.Bulanan.pustakawan;
      growthChart.update();
    }, 120);

    // Tab switcher periode (Bulanan, Mingguan, Tahunan)
    const segmentButtons = document.querySelectorAll('.growth-segment-btn');
    segmentButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        segmentButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const mode = btn.textContent.trim();
        if (datasetsByPeriod[mode]) {
          growthChart.data.labels = datasetsByPeriod[mode].labels;
          growthChart.data.datasets[0].data = datasetsByPeriod[mode].member;
          growthChart.data.datasets[1].data = datasetsByPeriod[mode].pustakawan;
          growthChart.options.scales.y.suggestedMax = mode === 'Tahunan' ? 3800 : (mode === 'Mingguan' ? 180 : 550);
          growthChart.update();
        }
      });
    });
  }

  // Chart 2: Status Kesehatan Akun (Aktif vs Ditangguhkan) with Guaranteed Entrance Animation
  const statusChartCanvas = document.getElementById('accountStatusChart');
  if (statusChartCanvas) {
    const ctx = statusChartCanvas.getContext('2d');
    const statusChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Akun Aktif', 'Akun Ditangguhkan'],
        datasets: [{
          data: [0, 0],
          backgroundColor: ['#10b981', '#f59e0b'],
          borderWidth: 4,
          borderColor: '#e6ecf5',
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '74%',
        animation: {
          animateRotate: true,
          animateScale: true,
          duration: 1600,
          easing: 'easeOutCubic'
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
            padding: 10,
            cornerRadius: 8
          }
        }
      }
    });

    // Trigger Entrance Doughnut Rotation and Bloom Animation
    setTimeout(() => {
      statusChart.data.datasets[0].data = [3406, 22];
      statusChart.update();
    }, 150);
  }
});
