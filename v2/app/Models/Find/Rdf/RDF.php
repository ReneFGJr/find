<?php

namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

helper('sisdoc');

class RDF extends Model
{
    var $table            = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cc_class',
        'cc_origin',
        'cc_pref_term',
        'cc_update',
        'cc_status',
        'cc_use'
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

    function index($d1, $d2, $d3, $d4, $cab) {}

    function le($id) {
        helper('sisdoc');
        $library = function_exists('get') ? get("library") : '';
        $ItemModel = new \App\Models\Find\Items\Index();

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

        $Class = $RSP['concept']['Class'];
        $IT = [];
        switch ($Class)
            {
                case 'Subject':
                    foreach ($RSP['data'] as $id => $line) {
                        if ($line['Property'] == 'hasSubject') {
                            $IT[] = $line['ID'];
                        }
                    }
                    $TT = $ItemModel
                            ->select('i_work')
                            ->wherein('i_manifestation', $IT)
                            ->where('i_library', $library)
                            ->groupby('i_work')
                            ->findAll();
                    $IT = [];
                    foreach($TT as $id=>$line)
                        {
                            $IT[] = $line['i_work'];
                        }
                break;
                case 'Person':
                    foreach($RSP['data'] as $id=>$line)
                        {
                            if ($line['Property'] == 'hasAuthor')
                                {
                                    $IT[] = $line['ID'];
                                }
                        }
                    break;
            }
        $cp = 'i_titulo as title, i_work as ID, i_identifier as isbn, i_library, "" as cover, count(*) as exemplares';
        $RSP['items'] = [];
        if (count($IT) == 0) { return $RSP; }

        $Covers = new \App\Models\Find\Cover\Index();

        $Items = $ItemModel
            ->select($cp)
            ->whereIn('i_work', $IT)
            ->where('i_library',$library)
            ->groupby('i_work, i_library')
            ->orderBy('i_titulo')
            ->findAll();

        foreach($Items as $id=>$line)
            {
                $cover = $Covers->cover($line['isbn']);
                $Items[$id]['cover'] = $cover;
            }
        $RSP['items'] = $Items;

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

        /* Adaptation for RDF3 */
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
    /* ($idDW, 'hasTitle', $idTitulo); */
    function createLiteral($idC,$property,$term,$lang='')
    {
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();
        $RDF_Class = new \App\Models\Find\Rdf\RDF_Class();

        /* Literal */
        $idN = null;
        if (is_numeric($term) && $lang == '') {
            $idN = (int) round($term);
        }

        if (!$idN) {
            echo "OPS, Literal invalido: $term";
            return null;
        }

        $idP = $RDF_Class->getIdByName($property, 'P');
        if ($idP > 0)
            {
                $RDF_Data->insert([
                    'd_r1' => $idC,
                    'd_p' => $idP,
                    'd_r2' => 0,
                    'd_literal' => $idN,
                    'd_creadted' => date('Y-m-d H:i:s'),
                    'd_update' => date('Y-m-d H:i:s'),
                    'd_library' => '',
                    'd_user' => ''
                ]);
                return $RDF_Data->getInsertID();
            }
    }

    function createConcept($Class, $Name, $Lang='pt_BR')
    {
        /******* Literal Name */
        $NameModel = new \App\Models\Find\Rdf\RDF_Name();
        $ClassModel =  new \App\Models\Find\Rdf\RDF_Class();
        $idN = $NameModel->createLiteral($Name, $Lang);

        /******* Check Exist Concept */
        $dt = $this
            ->select('id_cc')
            ->join('rdf_name', 'cc_pref_term = id_n', 'left')
            ->where('cc_pref_term', $idN)
            ->first();

        if (!$dt) {
            /* Recupeara Classe */
            $idC = $ClassModel->getIdByName($Class);
            /* Cria ou Recupera Conceito */
            $this->insert([
                'cc_class' => $idC,
                'cc_origin' => '',
                'cc_pref_term' => $idN,
                'cc_update' => date('Y-m-d'),
                'cc_status' => 0,
                'cc_use' => 1
            ]);
            $idCC = $this->getInsertID();
        } else {
            $idCC = $dt['id_cc'];
        }

        return ['status' => '200', 'message' => 'Conceito criado com sucesso', 'id_cc' => $idCC];
    }

}
