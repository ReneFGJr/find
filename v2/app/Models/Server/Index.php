<?php

namespace App\Models\Server;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function index($d1='', $d2='', $d3='', $d4='')
    {
        switch ($d1) {
            case 'status':
                return $this->getStatus();
            default:
                $RSP = [
                    'status' => '200',
                    'message' => 'API is running',
                    'data' => [
                        'version' => '2.0.0',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'server' => $_SERVER['SERVER_NAME'],
                        'php_version' => phpversion(),
                        'database' => $this->DBGroup,
                    ],
                ];
                return $RSP;
        }


        return $response;
    }
}
