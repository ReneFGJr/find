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

    function index($d1='',$d2='',$d3='',$d4='')
        {
            $sx = '==>'.$d1;

            switch($d1)
                {
                    case 'PlaceBookshelf':
                        $PlaceBookshelf = new \App\Models\Bookself\PlaceBookshelf();
                        $sx = $PlaceBookshelf->index($d2,$d3,$d4);
                        break;
                    default:
                        $menu[URL.MODULE.'admin/PlaceBookshelf'] = 'find.PlaceBookshelf';
                        $sx .= h('find.adminPlaceBookshelf');
                        $sx .= menu($menu);
                        break;        
                }

            $sx = bs(bsc($sx),12);
            return $sx;
        }
}
