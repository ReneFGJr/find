<?php

namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

class RDF extends Model
{
    var $table            = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
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

    function index($d1, $d2, $d3, $d4, $cab) {}

    function le($id) {
        $RSP = [];
        $RSP['id'] = $id;

        $dt = $this
            ->select('id_cc as id, n_name as name, n_lang as lang, c_class as Class, c_type as type, cc_use as use')
            ->join('rdf_class', 'cc_class = id_c', 'left')
            ->join('rdf_name', 'cc_pref_term = id_n', 'left')
            ->where('id_cc', $id)
            ->first() ;
        $RSP['concept'] = $dt;
        $RSP['data'] = $this->getData($id);
        return $RSP;
    }

    function getData($id) {
        $RSP = [];
        $cp = 'c_class as Property, c_type as type, c2.id_cc as ID, c2.cc_use as use, n_lang as Lang, n_name as Caption, id_c as IDClass, id_d as IDd, id_n as IdN';
        $cp1 = 'c_class as Property, "Literal" as type, id_cc as ID, cc_use as use, n_lang as Lang, n_name as Caption, id_c as IDClass, id_d as IDd, id_n as IdN';
        $dt1 = $this
            ->select($cp)
            ->join('rdf_data','d_r1 = id_cc')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_concept as c2', 'd_r2 = c2.id_cc')
            ->join('rdf_name', 'c2.cc_pref_term = id_n', 'left')
            ->where('rdf_concept.id_cc', $id)
            ->where('d_literal',0)
            ->findAll() ;

        /* Adaptation for RDF2 */
        $dt2 = $this
            ->select($cp)
            ->join('rdf_data', 'd_r2 = id_cc')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_concept as c2', 'd_r1 = c2.id_cc')
            ->join('rdf_name', 'c2.cc_pref_term = id_n', 'left')
            ->where('rdf_concept.id_cc', $id)
            ->where('d_literal', 0)
            ->findAll();
        $dt3 = $this
            ->select($cp1)
            ->join('rdf_data', 'd_r1 = id_cc')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_name', 'd_literal = id_n', 'left')
            ->where('rdf_concept.id_cc', $id)
            ->where('d_literal <> 0')
            ->findAll();


        $RSP = array_merge($dt1, $dt2, $dt3);
        return $RSP;
    }

    function extract($data,$property)
    {
        $RSP = [];
        if (!isset($data['data'])) { return $RSP; }

        foreach($data['data'] as $id=>$line)
            {
                if ($line['Property'] == $property)
                    {
                        $dd = [];
                        $dd['ID'] = $line['ID'];
                        $dd['use'] = $line['use'];
                        $dd['Lang'] = $line['Lang'];
                        $dd['Caption'] = $line['Caption'];
                        array_push($RSP, $dd);
                    }
            }
        return $RSP;
    }

}
