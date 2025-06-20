<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - AgroShield</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f4f8; color: #333; display: flex; min-height: 100vh; }
        .container { display: flex; width: 100%; }
        .sidebar { width: 260px; background-color: #1a202c; color: #e2e8f0; padding: 24px; display: flex; flex-direction: column; }
        .sidebar-header { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .sidebar-header .logo-icon { font-size: 28px; color: #48bb78; }
        .sidebar-header h1 { font-size: 24px; font-weight: 600; }
        .sidebar-nav ul { list-style-type: none; flex-grow: 1; }
        .sidebar-nav li a { display: flex; align-items: center; gap: 16px; padding: 14px; color: #a0aec0; text-decoration: none; border-radius: 8px; margin-bottom: 8px; transition: background-color 0.2s, color 0.2s; }
        .sidebar-nav li a:hover, .logout-link a:hover { background-color: #2d3748; color: #ffffff; }
        .sidebar-nav li a.active { background-color: #48bb78; color: #ffffff; font-weight: 600; }
        .sidebar-nav li a i { width: 20px; text-align: center; }
        .logout-link { margin-top: auto; }
        .logout-link a { display: flex; align-items: center; gap: 16px; padding: 14px; color: #a0aec0; text-decoration: none; border-radius: 8px; }

        .main-content { flex-grow: 1; padding: 24px 32px; background: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2532&auto=format&fit=crop') no-repeat center center/cover; position: relative; }
        .main-content::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px); z-index: 1; }
        .main-content > * { position: relative; z-index: 2; }
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .main-header h2 { font-size: 28px; font-weight: 600; }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-profile img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }

        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        .widget { background: rgba(255, 255, 255, 0.6); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .widget-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .widget-header h3 { font-size: 18px; font-weight: 600; color: #2d3748; }
        .widget-header i { color: #718096; }

        .status-widget { display: flex; align-items: center; gap: 24px; }
        .status-visual { text-align: center; }
        .status-visual .icon-container { width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; transition: background-color 0.5s ease; }
        .status-visual .icon-container i { font-size: 56px; color: white; }
        .status-terbuka { background-color: #f6e05e; } /* Kuning Cerah */
        .status-tertutup { background-color: #4299e1; } /* Biru Hujan */
        .status-text { font-size: 24px; font-weight: 700; }
        .text-terbuka { color: #b7791f; }
        .text-tertutup { color: #2b6cb0; }
        .status-reason { font-size: 14px; color: #718096; }
        .sensor-data { flex-grow: 1; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .sensor-item { background-color: rgba(255, 255, 255, 0.5); padding: 16px; border-radius: 12px; text-align: center; }
        .sensor-item i { font-size: 28px; margin-bottom: 8px; color: #2d3748; }
        .sensor-item .value { font-size: 22px; font-weight: 600; }
        .sensor-item .label { font-size: 14px; color: #718096; }

        .activity-log ul { list-style: none; max-height: 200px; overflow-y: auto; }
        .activity-log li { display: flex; justify-content: space-between; padding: 8px 4px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .activity-log li:last-child { border-bottom: none; }
        .activity-log .time { color: #718096; font-size: 12px; }
        .activity-log .status-icon { margin-right: 8px; }

        @media (max-width: 1200px) { .dashboard-grid { grid-template-columns: 1fr; } }
        @media (max-width: 992px) { .status-widget { flex-direction: column; } .sensor-data { width: 100%; } }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; flex-direction: row; justify-content: space-between; padding: 12px 24px; }
            .sidebar-header h1 { display: none; }
            .sidebar-nav ul { display: flex; flex-grow: 0; gap: 8px; }
            .sidebar-nav li a { padding: 10px; }
            .sidebar-nav li a span { display: none; }
            .logout-link { margin-top: 0; }
        }
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
                    <li><a href="#" class="active"><i class="fa-solid fa-border-all"></i> <span>Dashboard</span></a></li>
                    <li><a href="<?= base_url('analitik') ?>"><i class="fa-solid fa-chart-line"></i> <span>Analytics</span></a></li>
                    <li><a href="<?= base_url('weather') ?>"><i class="fa-solid fa-cloud-sun"></i> <span>Weather</span></a></li>
                    <li><a href="<?= base_url('chatbot') ?>"><i class="fa-solid fa-robot"></i> <span>ChatBot AI</span></a></li>
                <div class="logout-link">
                    <a href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span></a>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <header class="main-header">
                <div>
                    <h2>Selamat Datang, Petani!</h2>
                    <p style="color: #4a5568;">Status real-time sistem pengeringan AgroShield Anda.</p>
                </div>
                <div class="user-profile">
                    <img src="<?= base_url('assets/images/pp.jpg') ?>" alt="Foto Pengguna">
                    <span>JenTikRat</span>
                </div>
            </header>

            <div class="dashboard-grid">
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <section class="widget status-widget">
                        <div class="status-visual">
                            <div id="status-icon-container" class="icon-container <?= $latest_data && $latest_data['status_hujan'] == 1 ? 'status-tertutup' : 'status-terbuka' ?>">
                               <i id="status-icon" class="fa-solid <?= $latest_data && $latest_data['status_hujan'] == 1 ? 'fa-cloud-showers-heavy' : 'fa-sun' ?>"></i>
                            </div>
                            <span id="status-text" class="<?= $latest_data && $latest_data['status_hujan'] == 1 ? 'text-tertutup' : 'text-terbuka' ?>">
                                <?= $latest_data && $latest_data['status_hujan'] == 1 ? 'ATAP TERTUTUP' : 'ATAP TERBUKA' ?>
                            </span>
                            <p id="status-reason" class="status-reason">
                                <?= $latest_data ? ($latest_data['status_hujan'] == 1 ? 'Terdeteksi hujan' : 'Kondisi cuaca cerah') : 'Menunggu data...' ?>
                            </p>
                        </div>
                        <div class="sensor-data">
                            <div class="sensor-item">
                                <i class="fa-solid fa-temperature-half"></i>
                                <div id="suhu-value" class="value"><?= $latest_data ? number_format($latest_data['suhu'], 1) . '°C' : '-' ?></div>
                                <div class="label">Suhu</div>
                            </div>
                            <div class="sensor-item">
                                <i class="fa-solid fa-droplet"></i>
                                <div id="kelembapan-value" class="value"><?= $latest_data ? number_format($latest_data['kelembapan'], 1) . '%' : '-' ?></div>
                                <div class="label">Kelembapan</div>
                            </div>
                            <div class="sensor-item">
                                <i class="fa-solid fa-lightbulb"></i>
                                <div id="cahaya-value" class="value"><?= $latest_data ? ($latest_data['cahaya'] > 1000 ? 'Cerah' : 'Mendung') : '-' ?></div>
                                <div class="label">Cahaya</div>
                            </div>
                        </div>
                    </section>
                    <section class="widget">
                        <div class="widget-header">
                            <h3>Grafik Sensor</h3>
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <canvas id="sensorChart"></canvas>
                    </section>
                </div>
                <div class="widget activity-log">
                    <div class="widget-header">
                        <h3>Log Aktivitas Terkini</h3>
                        <i class="fa-solid fa-list-ul"></i>
                    </div>
                    <ul id="activity-log-list">
                        </ul>
                </div>
            </div>
        </main>
    </div>


    <script>
        // Inisialisasi Grafik
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const sensorChart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'Suhu (°C)', data: [], borderColor: 'red', yAxisID: 'y' },
                { label: 'Kelembapan (%)', data: [], borderColor: 'blue', yAxisID: 'y1' }
            ]},
            options: { responsive: true, scales: {
                x: { type: 'time', time: { unit: 'second', tooltipFormat: 'HH:mm:ss' } },
                y: { position: 'left', title: { display: true, text: 'Suhu (°C)' } },
                y1: { position: 'right', title: { display: true, text: 'Kelembapan (%)' }, grid: { drawOnChartArea: false } }
            }}
        });

        // Fungsi untuk mengambil dan memperbarui seluruh UI
        async function updateDashboard() {
            try {
                const response = await fetch('<?= base_url('dashboard/getdata') ?>'); 
                const data = await response.json();
                
                // 1. Update Widget Status
                const latest = data.latest;
                if (latest) {
                    const isHujan = latest.status_hujan == 1;
                    document.getElementById('suhu-value').textContent = parseFloat(latest.suhu).toFixed(1) + '°C';
                    document.getElementById('kelembapan-value').textContent = parseFloat(latest.kelembapan).toFixed(1) + '%';
                    document.getElementById('cahaya-value').textContent = latest.cahaya > 1000 ? 'Cerah' : 'Mendung';
                    
                    const statusIconContainer = document.getElementById('status-icon-container');
                    const statusIcon = document.getElementById('status-icon');
                    const statusText = document.getElementById('status-text');

                    statusIconContainer.className = 'icon-container ' + (isHujan ? 'status-tertutup' : 'status-terbuka');
                    statusIcon.className = 'fa-solid ' + (isHujan ? 'fa-cloud-showers-heavy' : 'fa-sun');
                    statusText.className = isHujan ? 'text-tertutup' : 'text-terbuka';
                    statusText.textContent = isHujan ? 'ATAP TERTUTUP' : 'ATAP TERBUKA';
                    document.getElementById('status-reason').textContent = isHujan ? 'Terdeteksi Hujan' : 'Kondisi Cuaca Cerah';
                }

                // 2. Update Log Aktivitas
                const logList = document.getElementById('activity-log-list');
                logList.innerHTML = ''; // Kosongkan list
                data.log.forEach(item => {
                    const isHujan = item.status_hujan == 1;
                    const logTime = new Date(item.timestamp).toLocaleTimeString('id-ID');
                    const iconClass = isHujan ? 'fa-solid fa-droplet status-icon' : 'fa-solid fa-sun status-icon';
                    const iconColor = isHujan ? 'blue' : '#e6a100';
                    const logText = isHujan ? 'Hujan terdeteksi, atap tertutup' : 'Cuaca cerah, atap terbuka';

                    logList.innerHTML += `<li>
                        <span><i class="${iconClass}" style="color:${iconColor};"></i> ${logText}</span> 
                        <span class="time">${logTime}</span>
                    </li>`;
                });

                // 3. Update Grafik
                const chartData = data.chart_data;
                sensorChart.data.labels = chartData.map(item => item.timestamp);
                sensorChart.data.datasets[0].data = chartData.map(item => item.suhu);
                sensorChart.data.datasets[1].data = chartData.map(item => item.kelembapan);
                sensorChart.update('none');

            } catch (error) {
                console.error("Gagal memperbarui dashboard:", error);
            }
        }

        // Panggil fungsi update setiap 5 detik
        setInterval(updateDashboard, 5000);
        // Panggil juga saat halaman pertama kali dimuat
        window.onload = updateDashboard;
    </script>
</body>
</html>