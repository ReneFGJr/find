<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFform2 extends Model
{
    protected $table            = 'rdf_form_class_2';
    protected $primaryKey       = 'id_form';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'form_property',
        'form_frbr',
        'form_library',
        'form_frbr',
        'form_group',
        'form_order'
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

    function checkRegister($itemId)
    {
        $RDFItem = new \App\Models\Find\Items\Index();
        $RDFdata = new \App\Models\FindServer\RDFdata();
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $RDFliteral = new \App\Models\FindServer\RDFliteral();

        $dd = $RDFItem->where('id_i', $itemId)->first();
        $workId = $dd['i_work'];
        $manId = $dd['i_manitestation'];
        $expId = $dd['i_expression'];

        /**************** Verificar Título do Work */
        $class = 'hasTitle';
        $title = $dd['i_titulo'];

        $IDclass = $RDFclass->where('c_class', $class)->first();

        /**************** Verifica se o Work tem título */
        $dt = $RDFdata
            ->where('d_p', $IDclass['id_c'])
            ->where('d_r1', $workId)
            ->where('d_r2', 0)
            ->first();

        if ($dt != []) {
            if ($dt['d_literal'] == 0) {
                $dtL = $RDFliteral->getLiteral($title, 'pt_BR', true);
                $idL = $dtL['id_n'];

                $dd = [];
                $dd['d_literal'] = $idL;
                $RDFdata->set($dd)->where('id_d', $dt['id_d'])->update();
            } else {
                $idL = $dt['d_literal'];
            }
            //$idL = $RDFliteral->get("X");
        } else {
            echo "OPS";
        }
        $RSP['status'] = '200';
        return $RSP;
    }

    function create_integrity($workId, $expId, $manId, $library, $itemId)
    {
        $RDFConcept = new \App\Models\FindServer\RDFconcept();
        $RDFItem = new \App\Models\Find\Items\Index();
        $LANG = 'pt_BR';
        /******************** Recupera Work */
        $dd = $RDFItem->where('id_i', $itemId)->first();
        $nameE = 'ISBN:' . $dd['i_identifier'] . ':book:' . $LANG;

        /********************* Cria Expression */
        if ($expId == '' or $expId == 0) {
            $class = "Expression";
            $expId = $RDFConcept->createConcept($class, $nameE, $LANG);
        }

        /********************* Cria Manifestation */
        if ($manId == '' or $manId == 0) {
            $nameM = 'ISBN:' . $dd['i_identifier'] . ':book:' . $LANG;
            $class = "Manifestation";
            $manId = $RDFConcept->createConcept($class, $nameM, $LANG);
        }
        $dd = [];
        $dd['i_expression'] = $expId;
        $dd['i_manitestation'] = $manId;
        $RDFItem->set($dd)->where('id_i', $itemId)->update();
        return true;
    }

    function check_integrity($workId, $expId, $manId, $library, $itemId, $createdField = false)
    {
        $RSP = ['status' => '200', 'message' => 'OK'];
        if (($workId == '') or ($workId == 0)) {
            $RSP = ['status' => '400', 'message' => 'Work ID is invalid.'];
        } else if (($expId == '') or ($expId == 0)) {
            $RSP = ['status' => '400', 'message' => 'Expression ID is invalid.'];
        } else if (($manId == '') or ($manId == 0)) {
            $RSP = ['status' => '400', 'message' => 'Manifestation ID is invalid.'];
        } else if (($itemId == '') or ($itemId == 0)) {
            $RSP = ['status' => '400', 'message' => 'Item ID is invalid.'];
        }
        if (($createdField) and ($RSP['status'] == '400')) {
            $this->create_integrity($workId, $expId, $manId, $library, $itemId);
            $RSP = $this->check_integrity($workId, $expId, $manId, $library, $itemId);
        }
        return $RSP;
    }

    function combine($WorkForm, $workData)
    {
        foreach ($WorkForm as $k => $v) {
            $property = $v['form_property'];
            foreach ($workData as $kk => $wd) {
                if ($wd['IDClass'] == $property) {
                    $WorkForm[$k]   ['data'][] = $wd;
                }
            }
        }
        return $WorkForm;
    }

    function getClassProperties()
    {
        $RSP = [];
        $cp = 'id_c, c_class, c_type';
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $data = $RDFclass
            ->select($cp)
            ->orderBy('c_class', '', false) // false desliga o "escape"
            ->findAll();
        foreach ($data as $dd) {
            $RSP[$dd['id_c']] = $dd;
        }
        return $RSP;
    }

    function editForm($id, $library)
    {
        /******************** Recupera Item */
        $RDFItem = new \App\Models\Find\Items\Index();
        $RDFdata = new \App\Models\Find\Rdf\RDF();
        $dd = $RDFItem->where('id_i', $id)->where('i_library', $library)->first();
        $workId = $dd['i_work'];
        $manId = $dd['i_manitestation'];
        $expId = $dd['i_expression'];

        /******************** Verifica integridade e errado, cria estrutura W, E, M*/
        $RSP = $this->check_integrity($workId, $expId, $manId, $library, $id, true);
        if (isset($RSP['status']) and $RSP['status'] != '200') {
            return $RSP;
        }
        /******************** Recupera Propriedades e Classes */
        $ClasseProperties = $this->getClassProperties();

        /******************** Recupera Formulário */
        $WorkForm = $this->where('form_frbr', 'W')
            ->join('rdf_class as c', 'c.id_c = form_property', 'left')
            ->where('form_library', $library)
            ->orwhere('form_library', '1000')
            ->orderBy('form_order')
            ->findAll();

        if ($WorkForm != []) {
            foreach ($WorkForm as $k => $v) {
                $RG = '';
                $Range = json_decode($v['form_range'], true);
                if (!is_array($Range)) {
                    $Range = [];
                }
                foreach ($Range as $rk => $rv) {
                    if (isset($ClasseProperties[$rv])) {
                        if ($RG != '') {
                            $RG .= '|';
                        }
                        $RG .= $ClasseProperties[$rv]['c_class'];
                    }
                }
               $WorkForm[$k]['form_range'] =  $RG;
            }
        }

        $workData = $RDFdata->getData($workId);
        $FORM = [];
        $FORM['W'] = $this->combine($WorkForm, $workData);
        return $FORM;

        $DTdata = $RDFdata->getData($id);
        pre($DTdata);
        $cp = 'form_frbr, form_frbr as GROUP, form_group as SUBGROUP, c_class as PROPERTY, form_property as PROPERTY_ID, form_order as ORDER';
        $cp = '*';
        $form = $this
            ->select($cp)
            ->join('rdf_class as c', 'c.id_c = form_property', 'left')
            ->join('rdf_data', '(rdf_data.d_p = form_property) and (rdf_data.d_r1 = ' . $id . ' or rdf_data.d_r2 = ' . $id . ')', 'left')
            ->join('rdf_name', 'd_literal = id_n', 'left')
            ->orderby('form_group, form_order')
            ->findAll();
        echo $this->getlastquery();
        pre($form);
        $Item = new \App\Models\Find\Items\Index();
        $DtiTEM = $Item->getPubItem($id);

        $RDFdata = new \App\Models\FindServer\RDFdata();
        $DTdata = $RDFdata->le($id);
        pre($DTdata);
        $RSP = [];
        $RDFclass = new \App\Models\FindServer\RDFclass();
        pre($id);
    }

    function property_save($type, $library)
    {
        $type = strtoupper(substr($type, 0, 1));
        $RSP = [];
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $wo   = get('WO');
        $wo = explode(',', $wo);

        $this->where('form_frbr', $type)
            ->where('form_library', $library)
            ->where('form_group', get("subgroup"))
            ->delete();

        $order = 0;

        foreach ($wo as $w) {
            $order++;
            $data = [
                'form_property' => $w,
                'form_frbr'    => $type,
                'form_library' => $library,
                'form_group'   => get("subgroup"),
                'form_order'   => $order
            ];
            $this->insert($data);
        }

        return $RSP;
    }

    public function moveProperty($type, $library, $subgroup, $id, $pos)
    {
        $library = get("library");
        $move = get("direction");
        $id = get("id");

        $dt = $this->find($id);
        $pos = $dt['form_order'];
        if ($move == 'down') {
            $this
                ->where('id_form', $id)
                ->set('form_order', $pos + 1)
                ->update();
        } else {
            $this
                ->where('id_form', $id)
                ->set('form_order', $pos - 1)
                ->update();
        }

        $RSP = $this->formByLibrary($library);
        return $RSP;
    }

    public function formByLibrary($libraryId)
    {
        $db = db_connect();

        $builder = $db->table('rdf_form_class_2 f')
            ->select('f.id_form, f.form_frbr, f.form_property as id_c, f.form_group, f.form_order, c.c_class')
            ->join('rdf_class c', 'c.id_c = f.form_property', 'left')
            ->join('rdf_form_class_group as g', 'g.gr_name = f.form_group', 'left')
            ->where('f.form_library', $libraryId)
            ->orderBy('g.gr_order, f.form_group');

        $rows = $builder->get()->getResultArray();

        // Agrupar pelo form_group
        $result = [];
        foreach ($rows as $row) {
            $group = $row['form_group'] ?? 'SEM_GRUPO';
            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            $result[$group][] = [
                'id_form' => $row['id_form'],
                'frbr'    => $row['form_frbr'],
                'id_c'    => $row['id_c'],
                'c_class' => $row['c_class'],
                'order'   => $row['form_order'],
            ];
        }

        return $result;
    }


    function property($type, $library)
    {
        $id = get("type");

        $type = strtoupper(substr($type, 0, 1));
        $RSP = [];
        $RDFdata = new \App\Models\FindServer\RDFdata();

        $RDFclass = new \App\Models\FindServer\RDFclass();
        $data = $RDFclass
            ->join('rdf_form_class_2 as sc', 'sc.form_property = id_c AND sc.form_library = "' . $library . '"', 'left')
            ->where('c_type', 'P')
            ->where('form_group', get("subgroup"))
            ->orderBy('c_class', '', false) // false desliga o "escape"
            ->findAll();

        if ($data == []) {
            $data = $RDFclass
                ->join('rdf_form_class_2 as sc', 'sc.form_property = id_c AND sc.form_library = "1000"', 'left')
                ->where('c_type', 'P')
                ->orderBy('c_class', '', false) // false desliga o "escape"
                ->findAll();
        }


        $WT = [];
        $WO = [];

        foreach ($data as $dd) {
            $idf = $dd['id_form'];
            $dd['Label'] = $dd['c_class'];
            if ($dd['form_frbr'] == $type) {
                $WT[] = $dd;
            } else {
                $WO[] = $dd;
            }
        }

        $RSP = [];
        $RSP['WO'] = $WT;
        $RSP['WT'] = $WO;
        $RSP['type'] = $type;
        $RSP['subgroup'] = get("subgroup");
        $RSP['library'] = $library;
        $RSP['status'] = '200';
        return $RSP;
    }
}
