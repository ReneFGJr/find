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
    protected $allowedFields    = [
        'd_r1','d_p','d_r2','d_literal','d_library','d_user'
    ];

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

    function index($d1, $d2, $d3, $d4, $cab)
    {
        $user = 0;
        $RSP = [];
        $RSP['id'] = $d1;
        $RSP['d2'] = $d2;
        $RSP['d3'] = $d3;
        $RSP['d4'] = $d4;

        switch ($d3) {
            case 'form':
                $RSP['post'] = $_POST;
                break;
            case 'saveRDFdata':
                $RDFdata = new \App\Models\FindServer\RDFdata();
                $RDFclass = new \App\Models\FindServer\RDFclass();
                $Class = $RDFclass->getClass(get("propriety"));
                $IDclass = $Class['id_c'];
                if ($IDclass == '') {
                    $RSP['message'] = 'Propriety not found';
                    $RSP['status'] = '400';
                    return $RSP;
                }
                $dt = [];
                $dt['d_r1'] = get("conceptID");
                $dt['d_p'] = $IDclass;
                $dt['d_r2'] = get("selectID");
                $dt['d_literal'] = 0;
                $dt['d_library'] = get("library") ?? '1000';
                $dt['d_user'] = $user;

                /****************** Verifica se não existe antes de inserir */
                $RDFdata->where('d_r1', $dt['d_r1']);
                $RDFdata->where('d_p', $dt['d_p']);
                $RDFdata->where('d_r2', $dt['d_r2']);
                $exists = $RDFdata->first();
                if (!$exists) {
                    $RSP['message'] = 'RDF data saved successfully';
                    $RSP['status'] = '200';
                    $RDFdata->set($dt)->insert();
                } else {
                    $RSP['message'] = 'RDF data already exists';
                    $RSP['status'] = '409';
                    return $RSP;
                }
                break;
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
