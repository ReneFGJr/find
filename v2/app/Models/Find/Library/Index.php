<?php

namespace App\Models\Find\Library;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library';
    protected $primaryKey       = 'id_l';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_l',
        'l_name',
        'l_code',
        'l_id',
        'l_logo',
        'l_about',
        'l_visible',
        'l_net'
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

    function createLibrary($d3)
    {
        $RSP = [];
        $RSP['post'] = $_POST;
        return $RSP;
    }

    function saveLibrary()
    {
        $RSP = [];
        $id = get("id_l");
        $dd = array_merge($_POST, $_GET);

        if ($id == '0') {
            $dt = $this->where('l_name', $dd['l_name'])->first();
            if ($dt) {
                $RSP['status'] = '400';
                $RSP['error'] = 'Já existe uma biblioteca com este nome.';
                return $RSP;
            }

            unset($dd['l_id']);
            $dd['l_code'] = '';
            $dd['l_id'] = '';
            $newId = $this->set($dd)->insert();

            // calcula o código e atualiza só os campos necessários
            $code = 1000 + $newId;
            $this->update($newId, [
                'l_code'   => $code,
                'l_idcode' => $code,
            ]);

            $RSP['library'] = $this->find($newId);
            $RSP['status'] = '200';
            return $RSP;

            $dd['l_code'] = 1000+$id;
            $dd['l_idcode'] = $dd['l_code'];
            $this->set($dd)->where('id_l',$id)->update();

        }
        $RSP['post'] = $dd;
        return $RSP;
    }

    function le($id)
    {
        $cp = 'l_name as Library, l_code as ID, l_logo as Logo';
        $dt = $this
            ->select($cp)
            ->where('l_id', $id)
            ->first();

        $logo = $dt['Logo'];
        if ($logo == '') {
            $logo = '/assets/icons/icone_library.png';
        } else {
            if (file_exists(FCPATH . '/assets/images/' . $logo)) {
                $logo = base_url('assets/images/' . $logo);
            } else {
                $logo = '/assets/icons/icone_library.png';
            }
        }
        $dt['Logo'] = $logo;

        return $dt;
    }

    function listAll()
    {
        $dt = $this
            ->orderBy('l_name')
            ->where('l_visible', 1)
            ->orderby('l_name')
            ->findAll();
        $LIBS = [];
        $url = base_url();
        $url = str_replace('v2/public', '', $url);
        foreach ($dt as $id => $line) {
            $lib = [];
            $code = $line['l_code'];
            $lib['name'] = $line['l_name'];
            $lib['code'] = $code;
            $lib['logo'] = $url . '/img/logo/logo_' . $line['l_code'] . '.jpg';
            $lib['about'] = $line['l_about'];
            array_push($LIBS, $lib);
        }
        return $LIBS;
    }
}
