<?php
namespace App\Models;
use CodeIgniter\Model;

class SensorModel extends Model
{
    protected $table = 'data_sensor';
    protected $allowedFields = ['suhu', 'kelembapan', 'cahaya', 'status_hujan', 'timestamp'];
}
