/**
 * DigiLib - Dashboard Specific Scripts & Chart.js Config
 */

document.addEventListener('DOMContentLoaded', () => {
  const chartCanvas = document.getElementById('circulationChart');
  if (!chartCanvas) return;

  const ctx = chartCanvas.getContext('2d');

  // Datasets for different filter views
  const dataStore = {
    Bulanan: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
      fisik: [560, 680, 800, 640, 870, 470, 400, 840, 1130, 1180, 920, 550],
      digital: [350, 470, 600, 440, 700, 320, 240, 680, 960, 1020, 750, 440]
    },
    Mingguan: {
      labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
      fisik: [310, 420, 380, 460],
      digital: [220, 310, 290, 370]
    },
    Tahunan: {
      labels: ['2020', '2021', '2022', '2023', '2024'],
      fisik: [6200, 7800, 9400, 11200, 12850],
      digital: [3100, 4500, 6800, 8900, 11200]
    }
  };

  // Warna bar: normal, sorot (Sep-Okt = puncak UTS), pudar (Nov-Des)
  const palette = {
    fisik:   { base: '#2f5bd8', deep: '#1d4ed8', faded: '#93a8ea' },
    digital: { base: '#0f8a5f', deep: '#047857', faded: '#7cc0a3' }
  };

  const barColors = (series, mode) => {
    const c = palette[series];
    if (mode !== 'Bulanan') return c.base;
    return dataStore.Bulanan.labels.map((_, i) =>
      (i === 8 || i === 9) ? c.deep : (i >= 10 ? c.faded : c.base)
    );
  };

  // Create Chart.js instance
  const circulationChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: dataStore.Bulanan.labels,
      datasets: [
        {
          label: 'Buku Fisik',
          data: dataStore.Bulanan.fisik,
          backgroundColor: barColors('fisik', 'Bulanan'),
          borderRadius: 6,
          barPercentage: 0.6,
          categoryPercentage: 0.7
        },
        {
          label: 'E-Book Digital',
          data: dataStore.Bulanan.digital,
          backgroundColor: barColors('digital', 'Bulanan'),
          borderRadius: 6,
          barPercentage: 0.6,
          categoryPercentage: 0.7
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false // Using custom neumorphic legend in HTML
        },
        tooltip: {
          backgroundColor: 'rgba(30, 41, 59, 0.9)',
          titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
          bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
          padding: 10,
          cornerRadius: 8,
          displayColors: true
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
          beginAtZero: true,
          suggestedMax: 1200,
          grid: {
            color: 'rgba(166, 180, 200, 0.2)',
            drawBorder: false
          },
          ticks: {
            font: { family: 'Plus Jakarta Sans', size: 10, weight: '500' },
            color: '#94a3b8',
            stepSize: 400,
            callback: (value) => {
              if (value >= 1000) return (value / 1000) + 'k';
              return value;
            }
          }
        }
      }
    }
  });

  // Segment Tab switching (Mingguan, Bulanan, Tahunan)
  const segmentBtns = document.querySelectorAll('.segment-btn');
  segmentBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      segmentBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const mode = btn.textContent.trim();
      if (dataStore[mode]) {
        circulationChart.data.labels = dataStore[mode].labels;
        circulationChart.data.datasets[0].data = dataStore[mode].fisik;
        circulationChart.data.datasets[1].data = dataStore[mode].digital;
        circulationChart.data.datasets[0].backgroundColor = barColors('fisik', mode);
        circulationChart.data.datasets[1].backgroundColor = barColors('digital', mode);

        const yScale = circulationChart.options.scales.y;
        yScale.ticks.stepSize = mode === 'Bulanan' ? 400 : undefined;
        yScale.suggestedMax = mode === 'Bulanan' ? 1200 : undefined;
        circulationChart.update();
      }
    });
  });

  // Quick Action Buttons Handlers
  const addTxBtn = document.querySelector('.btn-pill-primary');
  if (addTxBtn) {
    addTxBtn.addEventListener('click', () => {
      alert('Membuka modal / formulir Tambah Transaksi Peminjaman Baru...');
    });
  }

  const downloadBtn = document.querySelector('.btn-download-rekap');
  if (downloadBtn) {
    downloadBtn.addEventListener('click', () => {
      alert('Mengunduh rekap sirkulasi semester aktif (Format .PDF / .XLSX)...');
    });
  }
});