<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFconcept extends Model
{
    var $DBGroup                = 'rdf2';
    var $table                  = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cc','cc_class', 'cc_use', 'cc_pref_term',
        'c_equivalent', 'cc_origin', 'cc_status',
        'cc_update', 'cc_origin'
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

    function le($id)
        {
            $cp = 'id_cc, cc_use, prefix_ref, c_class, n_name, n_lang, id_n, cc_status, cc_created, cc_update, cc_version, id_c, cc_pref_term';
            //$cp = '*';
            $dc = $this
                ->select($cp)
                ->join('rdf_literal', 'cc_pref_term = id_n','left')
                ->join('rdf_class', 'id_c = cc_class')
                ->join('rdf_prefix', 'id_prefix = c_prefix')
                ->where('id_cc',$id)
                ->first();

            /* Data */
            $dc['status'] = '200';
            if ($dc['cc_status'] == 9)
                {
                    $dc['message'] = 'Register canceled';
                    $dc['status'] = '404';
                }

            return $dc;
        }

    function searchTerm($term,$class)
        {
            $cp = 'id_cc as ID, n_name as Term, cc_use as use';
            $dt = $this
                ->select($cp)
                ->join('brapci_rdf.rdf_literal','id_n = cc_pref_term')
                ->where('cc_class',$class)
                ->like('n_name',$term)
                ->orderBy('n_name')
                ->findAll();
            return $dt;
        }

    function createConcept($dt)
        {
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $d = [];

            /* Literal Value */
            $d['cc_pref_term'] = $RDFliteral->register($dt['Name'],$dt['Lang']);

            /********************* Classe */
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $class = $RDFclass->getClass($dt['Class']);
            $d['cc_class'] = $class;
            $d['cc_use'] = 0;
            $d['cc_origin'] = '';
            $d['cc_update'] = date("Y-m-d");
            $d['cc_status'] = 1;

            /* Verifica se existe a Classe */
            if ($d['cc_class'] <= 0) { return -1; }

            $DTI = $this
                ->where('cc_class',$class)
                ->where('cc_pref_term', $d['cc_pref_term'])
                ->first();

              if ($DTI != [])
                    {
                        $iDTC = $DTI['id_cc'];
                    } else {
                        $iDTC = $this->set($d)->insert();
                    }
            return $iDTC;
        }

    function totalProp($class)
        {
            return 0;
        }
    function totalClass($class)
        {
            $RDFclass = new \App\Models\RDF2\RDFclass;
            if (sonumero($class) != $class)
                {
                    $class = $RDFclass->getClass($class);
                }
            $dt = $this
                ->select('count(*) as total')
                ->where('cc_class',$class)
                ->first();
            if ($dt == null)
                {
                    return 0;
                }
            return($dt['total']);
        }

    function getData($class)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $class = $RDFclass->getClass($class);

            $dt = $RDFconcept->getClassRegisters($class);
            return $dt;
        }

    function getClassRegisters($class)
        {
            $cp = 'id_cc as ID, cc_use as use, n_name as label, c_class as Class';
            $dt = $this
                ->select($cp)
                ->join('rdf_literal', 'cc_pref_term = id_n')
                ->join('rdf_class', 'cc_class = id_c')
                ->where('cc_class',$class)
                ->findAll();
            return $dt;
        }

    function updateStatus($id,$status)
        {
            $dt['cc_status'] = $status;
            $this->set($dt)->where('id_cc',$id)->update();
        }

    function registerLiteral($idc,$name,$lang='',$prop='')
        {
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $RDFproperty = new \App\Models\RDF2\RDFproperty();
            $Language = new \App\Models\AI\NLP\Language();

            $id_prop = $RDFproperty->getProperty($prop);
            $name = $name;
            if ($lang == '')
                {
                    $lang = $Language->getTextLanguage($name);
                }

            $lock = 1;
            $lit = $RDFliteral->register($name, $lang, $lock);
            $RDFdata->register($idc, $id_prop, 0, $lit);
            return true;
        }
}
