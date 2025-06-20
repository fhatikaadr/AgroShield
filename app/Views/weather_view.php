<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast - AgroShield</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS TIDAK BERUBAH, SAMA SEPERTI SEBELUMNYA */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f4f8; color: #333; display: flex; min-height: 100vh; }
        .container { display: flex; width: 100%; height: 100%; }
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
        
        .weather-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            height: calc(100vh - 120px);
        }

        .current-weather-card {
            background-color: #2d3748;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .current-weather-icon { text-align: center; }
        .current-weather-icon i { font-size: 120px; color: #f6e05e; }
        .current-weather-info .temp { font-size: 88px; font-weight: 700; line-height: 1; }
        .current-weather-info .unit { font-size: 24px; vertical-align: top; }
        .current-weather-info .day { font-size: 18px; font-weight: 500; margin-top: 8px; }
        .current-weather-info .time { font-size: 14px; color: #a0aec0; }
        .divider { height: 1px; background-color: #4a5568; margin: 24px 0; }
        .current-weather-details .condition { font-size: 16px; font-weight: 500; margin-bottom: 8px; text-transform: capitalize;}
        .current-weather-details .rain { font-size: 14px; color: #a0aec0; }
        .location-card {
            background: url('https://i.ibb.co/6P1Gz53/bandung-map.png') no-repeat center center/cover;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            color: white;
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        .forecast-panel { display: flex; flex-direction: column; gap: 24px; }
        .weekly-forecast { display: flex; justify-content: space-between; gap: 16px; }
        .day-card {
            flex-grow: 1;
            background-color: #2d3748;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            border: 1px solid #4a5568;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .day-card:hover { transform: translateY(-5px); border-color: #48bb78; }
        .day-card.active { background-color: #48bb78; color: white; border-color: #48bb78; }
        .day-card .day-name { font-weight: 600; font-size: 14px; }
        .day-card i { font-size: 32px; margin: 12px 0; }
        .day-card .temp-range { font-size: 14px; }
        
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            background-color: #2d3748;
            padding: 24px;
            border-radius: 16px;
        }
        .highlight-card { background-color: #222938; padding: 20px; border-radius: 12px; }
        .highlight-card .label { color: #a0aec0; font-size: 14px; margin-bottom: 8px; }
        .highlight-card .value { font-size: 24px; font-weight: 600; }
        .highlight-card .value span { font-size: 16px; font-weight: 400; }

        .value {
            font-size: 24px;  /* Atau ukuran yang sesuai dengan elemen lain */
            font-weight: 600; /* Ketebalan font */
        }

        /* Class untuk warna UV */
        .uv-low { color: #28a745; }
        .uv-medium { color: #ffc107; }
        .uv-high { color: #fd7e14; }
        .uv-very-high { color: #dc3545; }
        .uv-extreme { color: #9333ea; }

        @media (max-width: 1200px) { .weather-grid { grid-template-columns: 1fr; height: auto; } }
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
                    <li><a href="<?= base_url('analitik') ?>"><i class="fa-solid fa-chart-line"></i> <span>Analytics</span></a></li>
                    <li><a href="<?= base_url('weather') ?>" class="active"><i class="fa-solid fa-cloud-sun"></i> <span>Weather</span></a></li>
                    <li><a href="<?= base_url('chatbot') ?>"><i class="fa-solid fa-robot"></i> <span>ChatBot AI</span></a></li>
                </ul>
                <div class="logout-link">
                    <a href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span></a>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <header class="main-header">
                <h2>Prakiraan Cuaca</h2>
                <p>Informasi cuaca terkini dan mingguan untuk lokasi Anda.</p>
            </header>

            <div class="weather-grid">
                <section class="current-weather-card">
                    <div>
                        <div class="current-weather-icon"><i id="current-icon" class="fa-solid fa-spinner fa-spin"></i></div>
                        <div class="current-weather-info">
                            <span id="current-temp" class="temp">--<span class="unit">°C</span></span>
                            <div id="current-day" class="day">Loading data...</div>
                        </div>
                    </div>
                    <div>
                        <div class="divider"></div>
                        <div class="current-weather-details">
                            <div id="current-condition" class="condition"><i class="fa-solid fa-circle-question"></i> ----</div>
                            <div id="current-rain" class="rain"><i class="fa-solid fa-umbrella"></i> Rain - --%</div>
                        </div>
                        <div id="location-name" class="location-card">
                           ----
                        </div>
                    </div>
                </section>

                <section class="forecast-panel">
                    <div id="weekly-forecast-container" class="weekly-forecast">
                        </div>
                    <div class="highlights-grid">
                        <div class="highlight-card">
                            <div class="label">UV Index</div>
                            <div id="uv-index" class="value">--</div>
                        </div>
                         <div class="highlight-card">
                            <div class="label">Wind Status</div>
                            <div id="wind-status" class="value">-- <span>km/h</span></div>
                        </div>
                         <div class="highlight-card">
                            <div class="label">Sunrise & Sunset</div>
                            <div id="sunrise-sunset" class="value" style="font-size: 18px;">
                                --:-- AM / --:-- PM
                            </div>
                        </div>
                         <div class="highlight-card">
                            <div class="label">Humidity</div>
                            <div id="humidity" class="value">-- <span>%</span></div>
                        </div>
                         <div class="highlight-card">
                            <div class="label">Visibility</div>
                            <div id="visibility" class="value">-- <span>km</span></div>
                        </div>
                         <div class="highlight-card">
                            <div class="label">Feels Like</div>
                            <div id="feels-like" class="value">-- <span>°C</span></div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- PENGATURAN API ---
        const API_KEY = '5e722718196b5da1c8a165c1ebd4c33a'; // <-- GANTI DENGAN API KEY ANDA DARI OPENWEATHERMAP
        const LAT = -6.9175;  // Latitude untuk Bandung
        const LON = 107.6191; // Longitude untuk Bandung
        
        // URL untuk mengambil data cuaca saat ini dan prakiraan
        const currentUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${LAT}&lon=${LON}&appid=${API_KEY}&units=metric`;
        const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?lat=${LAT}&lon=${LON}&appid=${API_KEY}&units=metric`;
        // Untuk UV Index, kita perlu panggilan API terpisah (jika tidak ada di paket utama)
        // const uvUrl = `https://api.openweathermap.org/data/2.5/uvi?lat=${LAT}&lon=${LON}&appid=${API_KEY}`;

        // Fungsi untuk mapping ikon dari OpenWeatherMap ke FontAwesome
        function mapOwmIconToFontAwesome(owmIcon) {
            const map = {
                '01d': 'fa-solid fa-sun',
                '01n': 'fa-solid fa-moon',
                '02d': 'fa-solid fa-cloud-sun',
                '02n': 'fa-solid fa-cloud-moon',
                '03d': 'fa-solid fa-cloud',
                '03n': 'fa-solid fa-cloud',
                '04d': 'fa-solid fa-cloud',
                '04n': 'fa-solid fa-cloud',
                '09d': 'fa-solid fa-cloud-showers-heavy',
                '09n': 'fa-solid fa-cloud-showers-heavy',
                '10d': 'fa-solid fa-cloud-sun-rain',
                '10n': 'fa-solid fa-cloud-moon-rain',
                '11d': 'fa-solid fa-cloud-bolt',
                '11n': 'fa-solid fa-cloud-bolt',
                '13d': 'fa-solid fa-snowflake',
                '13n': 'fa-solid fa-snowflake',
                '50d': 'fa-solid fa-smog',
                '50n': 'fa-solid fa-smog',
            };
            return map[owmIcon] || 'fa-solid fa-question-circle';
        }
        
        // Fungsi untuk format waktu dari UNIX timestamp
        function formatTime(unixTimestamp) {
            return new Date(unixTimestamp * 1000).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }

        // Fungsi untuk estimasi UV Index berdasarkan waktu dan cuaca
        function estimateUVIndex(hour, weatherCondition) {
            // Jika malam hari (setelah jam 18 atau sebelum jam 6)
            if (hour < 6 || hour >= 18) return 0;
            
            // Faktor dasar berdasarkan waktu
            let baseUV = 0;
            if (hour >= 10 && hour < 14) baseUV = 8; // Puncak siang
            else if (hour >= 8 && hour < 10) baseUV = 5; // Pagi
            else if (hour >= 14 && hour < 16) baseUV = 5; // Sore awal
            else if (hour >= 6 && hour < 8) baseUV = 2; // Pagi awal
            else if (hour >= 16 && hour < 18) baseUV = 2; // Sore akhir
            
            // Faktor koreksi berdasarkan kondisi cuaca
            const conditions = weatherCondition.toLowerCase();
            if (conditions.includes('clear')) return baseUV;
            else if (conditions.includes('cloud')) return Math.round(baseUV * 0.7);
            else if (conditions.includes('rain') || conditions.includes('drizzle')) return Math.round(baseUV * 0.3);
            else if (conditions.includes('thunderstorm') || conditions.includes('snow')) return Math.round(baseUV * 0.2);
            else return Math.round(baseUV * 0.5);
        }

        // Fungsi utama untuk mengambil dan menampilkan data
        async function fetchAndDisplayWeather() {

            try {
                // Ambil data cuaca saat ini dan prakiraan secara bersamaan
                const [currentResponse, forecastResponse] = await Promise.all([
                    fetch(currentUrl),
                    fetch(forecastUrl)
                ]);

                if (!currentResponse.ok || !forecastResponse.ok) {
                    throw new Error('Gagal mengambil data cuaca. Cek API Key atau koneksi Anda.');
                }
                
                const currentData = await currentResponse.json();
                const forecastData = await forecastResponse.json();
                
                // --- Update UI dengan data cuaca saat ini ---
                const mainIconClass = mapOwmIconToFontAwesome(currentData.weather[0].icon);
                document.getElementById('current-icon').className = mainIconClass;
                document.getElementById('current-temp').innerHTML = `${Math.round(currentData.main.temp)}<span class="unit">°C</span>`;
                
                const now = new Date();
                document.getElementById('current-day').textContent = now.toLocaleDateString('en-US', { weekday: 'long', hour: '2-digit', minute: '2-digit' });
                
                document.getElementById('current-condition').innerHTML = `<i class="${mainIconClass}"></i> ${currentData.weather[0].description}`;
                document.getElementById('current-rain').innerHTML = `<i class="fa-solid fa-umbrella"></i> Rain - ${forecastData.list[0].pop * 100}%`;
                document.getElementById('location-name').textContent = `${currentData.name}, ${currentData.sys.country}`;

                // --- Update Highlights ---
                const currentHour = now.getHours();
                const estimatedUV = estimateUVIndex(currentHour, currentData.weather[0].description);

                // Gunakan format yang sama dengan elemen lain
                // Ganti kode UV Index dalam fetchAndDisplayWeather (sekitar line 300)

                // Tentukan kelas warna UV
                let uvClass = '';
                if (estimatedUV < 3) uvClass = 'uv-low';
                else if (estimatedUV < 6) uvClass = 'uv-medium';
                else if (estimatedUV < 8) uvClass = 'uv-high';
                else if (estimatedUV < 11) uvClass = 'uv-very-high';
                else uvClass = 'uv-extreme';

                // PERBAIKAN: Jangan nested span dalam div.value
                // Terapkan kelas warna langsung ke div#uv-index dan simpan nilai di dalamnya
                document.getElementById('uv-index').className = `value ${uvClass}`;
                document.getElementById('uv-index').textContent = estimatedUV;
                document.getElementById('wind-status').innerHTML = `${(currentData.wind.speed * 3.6).toFixed(1)} <span>km/h</span>`;
                document.getElementById('sunrise-sunset').innerHTML = `<i class="fa-solid fa-arrow-up"></i> ${formatTime(currentData.sys.sunrise)} <br> <i class="fa-solid fa-arrow-down"></i> ${formatTime(currentData.sys.sunset)}`;
                document.getElementById('humidity').innerHTML = `${currentData.main.humidity} <span>%</span>`;
                document.getElementById('visibility').innerHTML = `${(currentData.visibility / 1000).toFixed(1)} <span>km</span>`;
                document.getElementById('feels-like').innerHTML = `${Math.round(currentData.main.feels_like)} <span>°C</span>`;

                // --- Proses dan tampilkan prakiraan mingguan ---
                const weeklyContainer = document.getElementById('weekly-forecast-container');
                weeklyContainer.innerHTML = ''; // Kosongkan kontainer
                
                // Ambil data unik per hari dari prakiraan 5 hari / 3 jam
                const dailyForecasts = {};
                forecastData.list.forEach(item => {
                    const date = new Date(item.dt_txt).toLocaleDateString('en-CA'); // format YYYY-MM-DD
                    if (!dailyForecasts[date]) {
                        dailyForecasts[date] = [];
                    }
                    dailyForecasts[date].push(item);
                });

                let count = 0;
                for (const date in dailyForecasts) {
                    if (count >= 7) break; // Batasi hingga 7 hari

                    const dayData = dailyForecasts[date];
                    const representativeData = dayData.find(d => d.dt_txt.includes("12:00:00")) || dayData[0]; // Ambil data jam 12 siang atau data pertama
                    
                    const temp_min = Math.min(...dayData.map(d => d.main.temp_min));
                    const temp_max = Math.max(...dayData.map(d => d.main.temp_max));
                    const dayName = new Date(representativeData.dt_txt).toLocaleDateString('en-US', { weekday: 'short' });
                    
                    const dayCard = document.createElement('div');
                    dayCard.className = 'day-card';
                    if (count === 0) {
                        dayCard.classList.add('active');
                    }
                    dayCard.innerHTML = `
                        <div class="day-name">${dayName}</div>
                        <i class="${mapOwmIconToFontAwesome(representativeData.weather[0].icon)}"></i>
                        <div class="temp-range">${Math.round(temp_max)}°/${Math.round(temp_min)}°</div>
                    `;
                    weeklyContainer.appendChild(dayCard);
                    count++;
                }
                
            } catch (error) {
                console.error("Error fetching weather data:", error);
                document.getElementById('current-day').textContent = 'Gagal memuat data.';
            }
        }
        
        // Panggil fungsi utama saat halaman dimuat
        fetchAndDisplayWeather();
    });
    </script>
</body>
</html>