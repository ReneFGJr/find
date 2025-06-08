<?php

namespace App\Models\User;

use CodeIgniter\Model;

class UserGroup extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users_group';
    protected $primaryKey       = 'id_gr';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'gr_name',
        'gr_hash',
        'gr_library'
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

    function groups($library = null)
    {
        $cp = 'gr_name, id_grm, grm_user, us_nome, us_nickname, id_us, id_gr';
        $dt = $this
        ->select($cp)
        ->join('users_group_members', 'grm_group = id_gr AND grm_library = \''.$library.'\'', 'left')
        ->join('users', 'id_us = grm_user', 'left')
        ->groupBy($cp)
        ->orderBy('gr_name, us_nome', 'ASC')
        ->findAll();

        $dd = [];
        $dn = [];
        foreach ($dt as $d) {
            $e = [];
            if (!isset($dn[$d['gr_name']])) {
                $dn[$d['gr_name']] = $d['id_gr'];
            }

            $e['id_gr'] = $d['id_grm'];
            $e['id_us'] = $d['id_us'];
            $e['name'] = $d['us_nome'];
            $e['nickname'] = $d['us_nickname'];
            if ($d['us_nome'] == '') {
                $dd[$d['gr_name']] = [];
            } else {
                $dd[$d['gr_name']][] = $e;
            }

        }
        $dr = [];
        foreach ($dd as $k => $v) {
            $dk = [];
            $dk['name'] = $k;
            $dk['id'] = $dn[$k];
            $dk['users'] = $v;
            $dr[] = $dk;
        }
        return $dr;
    }
}
