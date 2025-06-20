<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analics - AgroShield</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f4f8; color: #333; display: flex; min-height: 100vh; }
        .container { display: flex; width: 100%; height: 100%;}
        .sidebar { width: 260px; background-color: #1a202c; color: #e2e8f0; padding: 24px; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .sidebar-header .logo-icon { font-size: 28px; color: #48bb78; }
        .sidebar-header h1 { font-size: 24px; font-weight: 600; }
        .sidebar-nav { display: flex; flex-direction: column; flex-grow: 1; }
        .sidebar-nav ul { list-style-type: none; flex-grow: 1; }
        .sidebar-nav li a { display: flex; align-items: center; gap: 16px; padding: 14px; color: #a0aec0; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: background-color 0.2s, color 0.2s; }
        .sidebar-nav li a:hover, .logout-link a:hover { background-color: #2d3748; color: #ffffff; }
        .sidebar-nav li a.active { background-color: #48bb78; color: #ffffff; font-weight: 600; }
        .sidebar-nav li a i { width: 20px; text-align: center; }
        .logout-link { margin-top: auto; }
        .logout-link a { display: flex; align-items: center; gap: 16px; padding: 14px; color: #a0aec0; text-decoration: none; border-radius: 8px; }

        .main-content { flex-grow: 1; padding: 24px 32px; background-color: #222938; color: #e2e8f0; }
        .main-header { margin-bottom: 24px; }
        .main-header h2 { font-size: 28px; font-weight: 600; }
        .main-header p { color: #a0aec0; }
        
        .filter-bar { display: flex; gap: 16px; align-items: center; background-color: #1a202c; padding: 16px; border-radius: 12px; margin-bottom: 24px; }
        .filter-bar label { font-size: 14px; }
        .filter-bar input[type="date"] { background-color: #2d3748; border: 1px solid #4a5568; color: #e2e8f0; padding: 8px 12px; border-radius: 8px; font-family: 'Poppins', sans-serif; }
        .filter-bar button { background-color: #48bb78; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background-color 0.2s; }
        .filter-bar button:hover { background-color: #38a169; }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 24px; }
        .kpi-card { background-color: #2d3748; padding: 24px; border-radius: 12px; border-left: 5px solid #48bb78; }
        .kpi-card .label { font-size: 14px; color: #a0aec0; margin-bottom: 8px; }
        .kpi-card .value { font-size: 32px; font-weight: 700; }
        .kpi-card .unit { font-size: 18px; font-weight: 500; margin-left: 4px; }
        
        .chart-container { background-color: #2d3748; padding: 24px; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-shield-halved logo-icon"></i>
                <h1>AgroShield</h1>
            </div>
            <div class="sidebar-nav">
                <ul>
                    <li><a href="<?= base_url('/') ?>"><i class="fa-solid fa-border-all"></i> <span>Dashboard</span></a></li>
                    <li><a href="<?= base_url('analitik') ?>" class="active"><i class="fa-solid fa-chart-line"></i> <span>Analytics</span></a></li>
                    <li><a href="<?= base_url('weather') ?>"><i class="fa-solid fa-cloud-sun"></i> <span>Weather</span></a></li>                    
                    <li><a href="<?= base_url('chatbot') ?>"><i class="fa-solid fa-robot"></i> <span>ChatBot AI</span></a></li>
                </ul>
                <div class="logout-link">
                    <a href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span></a>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <header class="main-header">
                <h2>Analisis Data Historis</h2>
                <p>Analisis tren dan performa sistem pengeringan Anda dari waktu ke waktu.</p>
            </header>

            <section class="filter-bar">
                <label for="startDate">Dari Tanggal:</label>
                <input type="date" id="startDate">
                <label for="endDate">Sampai Tanggal:</label>
                <input type="date" id="endDate">
                <button id="filterBtn">Terapkan Filter</button>
            </section>

            <section class="kpi-grid">
                <div class="kpi-card">
                    <div class="label">Rata-rata Suhu</div>
                    <div id="avg-suhu" class="value">-</div>
                </div>
                <div class="kpi-card">
                    <div class="label">Rata-rata Kelembapan</div>
                    <div id="avg-kelembapan" class="value">-</div>
                </div>
                <div class="kpi-card">
                    <div class="label">Total Deteksi Hujan</div>
                    <div id="total-hujan" class="value">-</div>
                </div>
            </section>

            <section class="chart-container">
                <canvas id="analitikChart"></canvas>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('analitikChart').getContext('2d');
            const analitikChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Suhu (°C)', data: [], borderColor: '#e53e3e', yAxisID: 'y', tension: 0.1 },
                        { label: 'Kelembapan (%)', data: [], borderColor: '#3182ce', yAxisID: 'y1', tension: 0.1 }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { type: 'time', time: { unit: 'day', tooltipFormat: 'dd MMM yyyy HH:mm' }, ticks: { color: '#a0aec0' } },
                        y: { position: 'left', title: { display: true, text: 'Suhu (°C)', color: '#e2e8f0' }, ticks: { color: '#a0aec0' } },
                        y1: { position: 'right', title: { display: true, text: 'Kelembapan (%)', color: '#e2e8f0' }, grid: { drawOnChartArea: false }, ticks: { color: '#a0aec0' } }
                    },
                    plugins: { legend: { labels: { color: '#e2e8f0' } } }
                }
            });

            const filterBtn = document.getElementById('filterBtn');
            filterBtn.addEventListener('click', updateAnalytics);

            // Set default date range to the last 7 days
            const today = new Date();
            const lastWeek = new Date();
            lastWeek.setDate(today.getDate() - 7);
            document.getElementById('endDate').value = today.toISOString().split('T')[0];
            document.getElementById('startDate').value = lastWeek.toISOString().split('T')[0];

            // Trigger initial data load
            updateAnalytics();

            async function updateAnalytics() {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                if (!startDate || !endDate) {
                    alert('Silakan pilih rentang tanggal terlebih dahulu.');
                    return;
                }

                try {
                    // Tampilkan loading state
                    document.getElementById('avg-suhu').innerHTML = `Memuat...`;
                    document.getElementById('avg-kelembapan').innerHTML = `Memuat...`;
                    document.getElementById('total-hujan').innerHTML = `Memuat...`;
                    
                    const response = await fetch(`<?= base_url('analitik/getdata') ?>?start=${startDate}&end=${endDate}`);
                    const data = await response.json();

                    // Update KPI Cards
                    document.getElementById('avg-suhu').innerHTML = `${parseFloat(data.kpi.avg_suhu).toFixed(1)}<span class="unit">°C</span>`;
                    document.getElementById('avg-kelembapan').innerHTML = `${parseFloat(data.kpi.avg_kelembapan).toFixed(1)}<span class="unit">%</span>`;
                    document.getElementById('total-hujan').innerHTML = `${data.kpi.total_hujan}<span class="unit">Kali</span>`;

                    // Update Chart
                    analitikChart.data.labels = data.chart_data.map(item => item.timestamp);
                    analitikChart.data.datasets[0].data = data.chart_data.map(item => item.suhu);
                    analitikChart.data.datasets[1].data = data.chart_data.map(item => item.kelembapan);
                    analitikChart.update();

                } catch (error) {
                    console.error("Gagal memperbarui analitik:", error);
                    alert('Gagal memuat data. Silakan coba lagi.');
                }
            }
        });
    </script>
</body>
</html>