<?php

namespace App\Models\Report;

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

    function index($d1, $d2, $d3='')
    {
        $RSP = [];
        switch ($d1) {
            case 'item':
                $RSP = $this->report_itens($d2);
                break;
            default:
                $RSP = ['error' => 'Invalid report type'];
        }
        return $RSP;
    }

    function report_itens($d1)
    {
        $RSP = [];
        $library = get("library");
        $cp = 'i_tombo, i_titulo, i_status, i_ln1, i_ln2, i_ln3, i_ln4, i_identifier';

        $Item = new \App\Models\Find\Items\Index();
        $RSP['items'] = $Item->select($cp)->where('i_library', $library)->findAll();
        return $RSP;
    }
}
