<?php
namespace App\Controllers;
use App\Models\SensorModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    // Fungsi ini akan menampilkan halaman utama dashboard
    public function index()
    {   
        $model = new SensorModel();
        
        // Ambil data sensor terakhir untuk widget status
        $data['latest_data'] = $model->orderBy('id', 'DESC')->first();
        
        // Kirim data ke view
        return view('dashboard_view', $data);


    }

    // Fungsi ini akan menyediakan data JSON untuk di-fetch oleh JavaScript
    public function getData()
    {
        $model = new SensorModel();
        $response = [];
        
        // Data terbaru untuk widget status
        $response['latest'] = $model->orderBy('id', 'DESC')->first();
        
        // Data untuk log aktivitas (5 data terakhir)
        $response['log'] = $model->orderBy('id', 'DESC')->findAll(5);
        
        // Data untuk grafik (misal 30 data terakhir)
        // Kita balik urutannya agar grafik menampilkan data dari kiri (lama) ke kanan (baru)
        $response['chart_data'] = array_reverse($model->orderBy('id', 'DESC')->findAll(30));

        return $this->response->setJSON($response);
    }

    public function chatbot()
    {
        return view('chatbot_view');
    }

        public function analitik()
    {
        return view('analitik_view');
    }

    /**
     * Menyediakan data JSON untuk halaman Analitik berdasarkan rentang tanggal.
     */
    public function getAnalitikData()
    {
        $startDate = $this->request->getGet('start');
        $endDate = $this->request->getGet('end');

        // Validasi input tanggal
        if (empty($startDate) || empty($endDate)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter tanggal start dan end diperlukan.']);
        }

        $model = new SensorModel();

        // Ambil semua data dalam rentang tanggal
        $queryResult = $model->where('timestamp >=', $startDate . ' 00:00:00')
                             ->where('timestamp <=', $endDate . ' 23:59:59')
                             ->orderBy('timestamp', 'ASC')
                             ->findAll();

        $response = [
            'kpi' => [
                'avg_suhu' => 0,
                'avg_kelembapan' => 0,
                'total_hujan' => 0
            ],
            'chart_data' => []
        ];

        if (!empty($queryResult)) {
            $totalSuhu = 0;
            $totalKelembapan = 0;
            $hujanCount = 0;
            $lastStatusHujan = -1; // Status awal, -1 agar data pertama terhitung jika hujan

            foreach ($queryResult as $row) {
                $totalSuhu += (float)$row['suhu'];
                $totalKelembapan += (float)$row['kelembapan'];

                // Hitung transisi dari tidak hujan (0) ke hujan (1)
                if ($row['status_hujan'] == 1 && $lastStatusHujan == 0) {
                    $hujanCount++;
                }
                $lastStatusHujan = $row['status_hujan'];
            }

            $dataCount = count($queryResult);
            $response['kpi']['avg_suhu'] = $totalSuhu / $dataCount;
            $response['kpi']['avg_kelembapan'] = $totalKelembapan / $dataCount;
            $response['kpi']['total_hujan'] = $hujanCount;
            $response['chart_data'] = $queryResult;
        }

        return $this->response->setJSON($response);
    }
    public function weather()
    {
        return view('weather_view');
    }
}