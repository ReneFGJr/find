<?php

namespace App\Models\User;

use CodeIgniter\Model;

class UserGroupMember extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users_group_members';
    protected $primaryKey       = 'id_grm';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_grm',
        'grm_group',
        'grm_user',
        'grm_library',
        'grm_status'
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

    function addToGroup($us, $group, $library)
    {
        $RSP = [];
        $dd = $this->select('*')
            ->where('grm_group', $group)
            ->where('grm_user', $us)
            ->where('grm_library', $library)
            ->findAll();
        if (count($dd) > 0) {
            $RSP['status'] = '500';
            $RSP['message'] = 'Usuário já está no grupo';
        } else {
            $data = [
                'grm_group' => $group,
                'grm_user' => $us,
                'grm_library' => $library
            ];
            $this->set($data)->insert();
            $RSP['status'] = '200';
            $RSP['message'] = 'Usuário adicionado ao grupo com sucesso';
        }
        return $RSP;
    }

    function disableMember($id_grm)
    {
        $row = $this->find($id_grm);
        if (!$row) {
            return ['status' => '404', 'message' => 'Registro não encontrado'];
        }
        $this->update($id_grm, ['grm_status' => 0]);
        return ['status' => '200', 'message' => 'Papel desabilitado com sucesso'];
    }
}
