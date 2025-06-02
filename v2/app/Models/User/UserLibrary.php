<?php

namespace App\Models\User;

use CodeIgniter\Model;
helper(['nbr']);

class UserLibrary extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users_library';
    protected $primaryKey       = 'id_ul';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_ul', 'ul_user', 'ul_library'];

    // Dates
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

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

    function updateUserLibrary($id_us,$library)
    {
        if(!is_numeric($id_us)) {
            return 500;
        }
        if(!is_numeric($library)) {
            return 501;
        }
        $dt = $this->where('ul_user', $id_us)
            ->where('ul_library', $library)
            ->first();
        if ($dt) {
            return 100;
        } else {
            $data = [
                'ul_user' => $id_us,
                'ul_library' => $library
            ];
            $this->set($data)->insert();
            return 200;
        }
    }
}