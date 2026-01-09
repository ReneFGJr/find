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
        'form_property', 'form_frbr', 'form_library',
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
