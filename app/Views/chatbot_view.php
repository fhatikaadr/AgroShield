<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot AI - AgroShield</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* --- [ STYLE ASLI DARI DASHBOARD ANDA ] --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f4f8; color: #333; display: flex; min-height: 100vh; }
        .container { display: flex; width: 100%; }
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

        .main-content { flex-grow: 1; padding: 0; background-color: #2d3748; display: flex; }

        /* --- [ STYLE BARU UNTUK HALAMAN CHATBOT ] --- */
        .chat-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .chat-history-panel {
            width: 280px;
            background-color: #1a202c;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #2d3748;
            flex-shrink: 0;
        }

        .new-chat-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background-color: #2d3748;
            color: #e2e8f0;
            border: 1px solid #4a5568;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 24px;
            transition: background-color 0.2s;
        }
        .new-chat-btn:hover { background-color: #4a5568; }
        .new-chat-btn span { font-weight: 500; }
        
        .history-list h4 {
            color: #a0aec0;
            font-size: 14px;
            font-weight: 600;
            margin-top: 16px;
            margin-bottom: 8px;
            padding: 0 4px;
        }

        .history-list ul {
            list-style: none;
        }

        .history-list li a {
            display: block;
            color: #a0aec0;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .history-list li a:hover, .history-list li a.active {
            background-color: #2d3748;
            color: #ffffff;
        }
        
        .chat-interface {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Pusatkan konten secara vertikal */
            align-items: center;
            padding: 40px;
            position: relative;
            background-color: #222938;
            height: 100%;
        }

        .chat-intro {
            text-align: center;
            color: #e2e8f0;
        }

        .farmer-mascot {
            width: 120px; /* Ukuran bisa disesuaikan */
            height: 120px;
            margin-bottom: 24px;
        }
        
        .chat-intro h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .chat-intro p {
            color: #a0aec0;
            margin-bottom: 32px;
        }
        
        .suggestion-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            width: 100%;
            max-width: 600px; /* Batasi lebar maksimum */
        }
        
        .suggestion-box {
            background-color: #2d3748;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #4a5568;
            cursor: pointer;
            transition: background-color 0.2s;
            color: #e2e8f0;
        }
        .suggestion-box:hover {
            background-color: #4a5568;
        }
        .suggestion-box .title {
            font-weight: 600;
            font-size: 16px;
        }
        .suggestion-box .description {
            font-size: 14px;
            color: #a0aec0;
        }
        
        .chat-input-bar {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 700px;
            padding: 0 20px;
        }
        
        .input-wrapper {
            position: relative;
        }

        .chat-input-bar input {
            width: 100%;
            height: 50px;
            border-radius: 12px;
            border: 1px solid #4a5568;
            background-color: #1a202c;
            color: #e2e8f0;
            padding: 0 50px 0 20px;
            font-size: 16px;
        }
        .chat-input-bar input:focus {
            outline: none;
            border-color: #48bb78;
        }

        .send-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #48bb78;
            color: white;
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .chat-intro img { width: 100px; height: 75px; object-fit: cover; }


        @media (max-width: 1200px) {
            .chat-history-panel { display: none; } /* Sembunyikan panel riwayat di layar kecil */
        }
         @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; flex-direction: row; justify-content: space-between; padding: 12px 24px; }
            .sidebar-header h1 { display: none; }
            .sidebar-nav { flex-direction: row; flex-grow: 0; }
            .sidebar-nav ul { display: flex; gap: 8px; }
            .sidebar-nav li a { padding: 10px; }
            .sidebar-nav li a span { display: none; }
            .logout-link { margin-top: 0; }
            .chat-container { height: calc(100vh - 68px); } /* Sesuaikan tinggi dengan sidebar */
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
                    <li><a href="<?= base_url('/') ?>"><i class="fa-solid fa-border-all"></i> <span>Dashboard</span></a></li>
                    <li><a href="<?= base_url('analitik') ?>"><i class="fa-solid fa-chart-line"></i> <span>Analytics </span></a></li>
                    <li><a href="<?= base_url('weather') ?>"><i class="fa-solid fa-cloud-sun"></i> <span>Weather</span></a></li>        
                    <li><a href="<?= base_url('chatbot') ?>" class="active"><i class="fa-solid fa-robot"></i> <span>ChatBot AI</span></a></li>
                </ul>
                <div class="logout-link">
                    <a href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span></a>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <div class="chat-container">
                <aside class="chat-history-panel">
                    <a href="#" class="new-chat-btn">
                        <span>Buat Obrolan Baru</span>
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <div class="history-list">
                        <h4>Hari Ini</h4>
                        <ul>
                            <li><a href="#" class="active">Strategi pemupukan padi IR64</a></li>
                            <li><a href="#">Cara mengatasi hama wereng</a></li>
                        </ul>
                        <h4>Kemarin</h4>
                        <ul>
                            <li><a href="#">Jadwal irigasi terbaik musim kemarau</a></li>
                        </ul>
                    </div>
                </aside>

                <section class="chat-interface">
                    <div class="chat-intro">
                        <img src="<?= base_url('assets/images/petani.png') ?>" alt="Petani">
                        <h2>Halo! Saya AgroBot.</h2>
                        <p>Ada yang bisa saya bantu seputar pertanian hari ini?</p>
                    </div>

                    <div class="suggestion-grid">
                        <div class="suggestion-box">
                            <div class="title">Prediksi Hama & Penyakit</div>
                            <div class="description">Analisa foto tanaman untuk deteksi dini</div>
                        </div>
                        <div class="suggestion-box">
                            <div class="title">Rekomendasi Pupuk</div>
                            <div class="description">Dapatkan saran pemupukan sesuai jenis tanah</div>
                        </div>
                        <div class="suggestion-box">
                            <div class="title">Strategi Irigasi</div>
                            <div class="description">Buatkan jadwal penyiraman yang efisien</div>
                        </div>
                        <div class="suggestion-box">
                            <div class="title">Informasi Harga Pasar</div>
                            <div class="description">Cek harga komoditas terkini di pasar lokal</div>
                        </div>
                    </div>
                    
                    <div class="chat-input-bar">
                        <div class="input-wrapper">
                            <input type="text" placeholder="Tanyakan apa saja pada AgroBot...">
                            <button class="send-btn"><i class="fa-solid fa-paper-plane"></i></button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>