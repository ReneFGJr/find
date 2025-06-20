<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
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

    function index($d1, $d2, $d3='')
    {
        $RSP = [];
        switch ($d1) {
            case 'users':
                $User = new \App\Models\User\User();
                $RSP = $User->index($d2, $d3);
                break;
            case 'group':
                switch ($d2) {
                    case 'assignGroup':
                        $UserGroup = new \App\Models\User\UserGroup();
                        $gr = get('id_gr');
                        $library = get('library');
                        $RSP = $UserGroup->list($gr, $library);
                        break;
                    case 'addToGroup':
                        $UserGroup = new \App\Models\User\UserGroup();
                        $RSP = $UserGroup->addToGroup($_POST);
                        break;
                    default:
                        $UserGroup = new \App\Models\User\UserGroup();
                        $RSP = $UserGroup->where('id_gr', $d2)->first();
                        break;
                }
                break;
            case 'groups':
                $UserGroup = new \App\Models\User\UserGroup();
                $RSP = $UserGroup->groups($d2);
                break;
        }
        return $RSP;
    }
}
