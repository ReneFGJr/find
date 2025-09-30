<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFform extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'rdf_form_class';
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

    function getForm($id,$library)
    {
        $Item = new \App\Models\Find\Items\Index();
        $dd = $Item->getItem($id,$library);
        if ($dd == []) {
            return ['status'=>'404','message'=>'Item not found'];
        }


        $Work = $dd['i_work'];
        $Manifestation = $dd['i_manitestation'];
        $Expression = $dd['i_expression'];

        $RDF = new \App\Models\Find\Rdf\RDF();
        $dd = $RDF->le($id);

        $dt = $this
            ->select('sc_group as SUBGROUP, c1.c_class as GROUP, c2.c_class as PROP, c1.c_type as c_type, c1.c_class as c_class, sc_library')
            ->join('rdf_class as c1', 'c1.id_c = sc_class', 'left')
            ->join('rdf_class as c2', 'c2.id_c = sc_propriety')
            ->where('sc_library',$library)
            ->OrWhere('sc_library','1000')
            ->groupBy('sc_group, c1.c_class, c2.c_class, c1.c_order, c2.c_order, sc_library, c1.c_type')
            ->orderBy('sc_group, c1.c_order, c2.c_order','ASC')
                ->findAll();

        $RSP = [];
        foreach ($dt as $k => $v) {
            $GROUP = $v['GROUP'];
            if ($GROUP == '') {
                $GROUP = 'Geral';
            }
            $dd['Class'] = $v['c_class'];
            $dd['Type'] = $v['c_type'];
            $dd['Property'] = $v['PROP'];
            $dd['Library'] = $v['sc_library'];
            $dd['Label'] = lang('App.'.$v['PROP']);
            $dd['value'] = [];

            $RSP[$GROUP][]=$dd;
        }
        return $RSP;
    }
}
