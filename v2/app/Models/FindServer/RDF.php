<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDF extends Model
{
    protected $table            = 'rdfs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function index($d1, $d2, $d3, $d4, $cab) {
        $RSP = [];
        $RSP['id'] = $d1;
        $RSP['d2'] = $d2;
        $RSP['d3'] = $d3;
        $RSP['d4'] = $d4;

        switch($d3) {
            case 'update':
                $RDFliteral = new \App\Models\FindServer\RDFliteral();
                $dd = [];
                $dd['n_name'] = $d4;
                $RDFliteral->set($dd)->where('id_n', $d2)->update();
                $RSP['message'] = 'Literal updated successfully';
                $RSP['status'] = '200';
                break;
        }

        return $RSP;
    }
}
