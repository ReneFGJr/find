<?php

namespace App\Models\Tombo;

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
        if ($d1 =='view') { $d1 = 'v'; }

        $RSP['action'] = $d1;
        switch($d1) {
            case 'v':
                $Item = new \App\Models\Find\Items\Index();
                $Library = new \App\Models\Find\Library\Index();
                $TomboID = get("tomboID");
                $lib = get("library");
                $RSP['library'] = $Library->le($lib);
                $RSP['item'] = $Item->getItemTombo($TomboID, $lib);
                $RSP['data'] = $_POST;
                break;

            default:
                $RSP['error'] = 'Invalid action';
        }
        return $RSP;
    }
}
