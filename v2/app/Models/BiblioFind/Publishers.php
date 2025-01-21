<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

class Publishers extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'find_publisher';
    protected $primaryKey       = 'id_pb';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pb_name',
        'pb_place',
        'pb_rdf',
        'pb_use'
    ];

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

    function register($pb_name)
    {
        $dt = $this->where('pb_name', $pb_name)->first();
        if (!$dt) {
            $dd = [];
            $dd['pb_name'] = $pb_name;
            $this->insert($dd);
            $dt = $this->where('pb_name', $pb_name)->first();
        }
        return $dt['id_pb'];
    }
}
