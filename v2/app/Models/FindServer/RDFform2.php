<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFform2 extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'rdf_form_class_2';
    protected $primaryKey       = 'id_form';
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

    function property($type,$library)
    {
        $type = strtoupper(substr($type,0,1));
        $RSP = [];
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $data = $RDFclass
            ->join('rdf_form_class_2 as sc', 'sc.form_property = id_c', 'left')
            ->findAll();

        $W = [];



        foreach ($data as $dd) {
            $idf = $dd['id_form'];
            if ($type == 'W' && ($idf != ''))     {
                $W[] = $dd;
            }

        }

        pre($W);

        return $RSP;
    }
}
