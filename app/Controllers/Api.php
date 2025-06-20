<?php
namespace App\Controllers;
use App\Models\SensorModel;
use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    protected $modelName = 'App\Models\SensorModel';
    protected $format = 'json';

    public function create()
    {
        $data = [
            'suhu'         => $this->request->getPost('suhu'),
            'kelembapan'   => $this->request->getPost('kelembapan'),
            'cahaya'       => $this->request->getPost('cahaya'),
            'status_hujan' => $this->request->getPost('status_hujan'),
        ];

        // Gunakan $this->model, bukan $model
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Data saved to local DB']);
        } else {
            // Gunakan $this->model, bukan $model
            return $this->failValidationErrors($this->model->errors());
        }
    }
}