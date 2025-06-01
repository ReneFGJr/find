<?php

namespace App\Models\User;

use CodeIgniter\Model;

class User extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id_us';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['us_nome', 'us_email', 'us_login', 'us_password', 'us_oauth2'];

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

    function index($d1, $d2, $d3 = '')
    {
        $RSP = [];
        switch ($d1) {
            case 'list':
                $RSP = $this->listUsers();
                break;
            case 'create':
                $RSP = $this->createUser($d2);
                break;
            case 'update':
                $RSP = $this->updateUser($d2, $d3);
                break;
            case 'delete':
                $RSP = $this->deleteUser($d2);
                break;
            default:
                $RSP = ['error' => 'Invalid user operation'];
        }
        return $RSP;
    }

    function listUsers()
    {
        $cp = 'us_nome, us_email, us_login, us_last, us_image, us_genero, us_cadastro';

        $dt = $this
            ->select($cp)
            ->orderBy('us_nome', 'ASC')
            ->findAll();
        return $dt;
    }
}
