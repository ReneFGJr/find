<?php

namespace App\Models\User;

use CodeIgniter\Model;
helper(['nbr']);

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
            case 'search':
                $RSP = $this->searchUser();
                break;
            case 'list':
                $RSP = $this->listUsers();
                break;
            case 'details':
                $RSP = $this->detailsUser($d2);
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
            case 'new':
                $RSP = $this->createUser();
                break;
            case 'inactivate':
                $RSP = $this->inactivateUser($d2);
                break;
            case 'activate':
                $RSP = $this->activateUser($d2);
                break;
            default:
                $RSP = ['error' => 'Invalid user operation'];
        }
        return $RSP;
    }

    function activateUser()
    {
        $UserLibrary = new \App\Models\User\UserLibrary();
        $userID = get("userID");
        $library = get("library");
        if (!is_numeric($userID)) {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Invalid user ID:'.$userID;
            $RSP['data'] = $_POST;
            return $RSP;
        }
        if ($library == '') {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Library code is required';
            return $RSP;
        }
        //$this->set(['us_ativo' => 1])->where('us_login', $login)->update();
        $st = $UserLibrary->updateUserLibrary($userID, $library);
        $RSP = [];
        $RSP['status'] = '200';
        $RSP['message'] = 'User activated successfully:'. $userID;
        return $RSP;
    }

    function inactivateUser()
    {
        $UserLibrary = new \App\Models\User\UserLibrary();
        $userID = get("userID");
        $library = get("library");
        $UserLibrary
            ->where(['ul_library' => $library])
            ->where('ul_user', $userID)
            ->delete();
        $RSP = [];
        $RSP['status'] = '200';
        $RSP['message'] = 'User desactivated successfully:'.$userID;
        return $RSP;
    }

    function createUser()
    {
        $data = [
            'us_nome' => nbr_author(get("us_nome"),7),
            'us_email' => get("us_email"),
            'us_login' => get("us_login"),
            'us_password' => get("us_senha"),
            'us_oauth2' => get("us_oauth2"),
        ];

        if ($data['us_login'] == '')
            {
                $data['us_login'] = $data['us_email'];
            }

        // Check if user login already exists
        $dt = $this
            ->where('us_login', $data['us_login'])
            ->first();

        if (is_array($dt))
            {
                $RSP = [];
                $RSP['status'] = '500';
                $RSP['message'] = 'User login already exists:'.$data['us_login'];
                $userID = $dt['id_us'];
            } else {
                $RSP = [];
                $RSP['status'] = '200';
                $RSP['message'] = 'User created successfully';
                $RSP['library'] = get("library");
                $userID = $this->insert($data);
            }

        $UserLibrary = new \App\Models\User\UserLibrary();
        $library = get("library");
        $st = $UserLibrary->updateUserLibrary($userID,$library);
        switch ($st) {
            default:
                $RSP['status'] = $st;
                $RSP['message'] = 'User library not found';
        }
        return $RSP;
    }

    function searchUser()
    {
        $d2 = get("q");
        $cp = 'id_us, us_nome, us_email, us_login, us_last, us_image, us_genero, us_cadastro, ul_library';
        $dt = $this
            ->select($cp)
            ->join('users_library', 'ul_user = id_us', 'left')
            ->like('us_nome', $d2)
            ->orderBy('us_nome', 'ASC')
            ->findAll(20);
        return $dt;
    }

    function updateUser()
    {
        $id = get("id_us");
        if (!is_numeric($id)) {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Invalid user ID:'.$id;
        }
        $data = [
            'us_nome' => nbr_author(get("us_nome"),7),
            'us_email' => get("us_email"),
            'us_login' => get("us_login"),
            'us_password' => get("us_password"),
            'us_oauth2' => get("us_oauth2"),
        ];
        $this->set($data)->where('id_us',get("id_us"))->update();
        $RSP = [];
        $RSP['status'] = '200';
        $RSP['message'] = 'User updated successfully';
        $RSP['library'] = get("library");

        $UserLibrary = new \App\Models\User\UserLibrary();
        $library = get("library");
        $st = $UserLibrary->updateUserLibrary($id,$library);
        switch ($st) {
            default:
                $RSP['status'] = $st;
                $RSP['message'] = 'User library not found';
        }
        return $RSP;
    }

    function detailsUser($id)
    {
        if (!is_numeric($id)) {
            return ['error' => 'Invalid user ID', 'id' => $id];
        }
        $cp = '*';
        $dt = $this
            ->select($cp)
            ->where('id_us', $id)
            ->first();
        return $dt;
    }

    function listUsers()
    {
        $cp = 'id_us, us_nome, us_email, us_login, us_last, us_image, us_genero, us_cadastro, ul_library';

        $dt = $this
            ->select($cp)
            ->join('users_library', 'ul_user = id_us', 'left')
            ->orderBy('us_nome', 'ASC')
            ->findAll();
        return $dt;
    }
}
