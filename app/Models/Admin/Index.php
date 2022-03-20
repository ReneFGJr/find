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

    function index($d1='',$d2='',$d3='',$d4='',$d5='')
        {
            $sx = '';
            switch($d1)
                {
        			case 'Library':
                        $Library = new \App\Models\Library\Libraries();
                        $sx .= $Library->index($d2,$d3,$d4,$d5);
                        break;                        
                    case 'PlaceBookshelf':
                        $PlaceBookshelf = new \App\Models\Bookself\PlaceBookshelf();
                        $sx = $PlaceBookshelf->index($d2,$d3,$d4);
                        break;
                    default:
                        $sx .= '==>'.$d1;
                        $menu[URL.MODULE.'admin/PlaceBookshelf'] = 'find.PlaceBookshelf';
                        $menu[URL.MODULE.'admin/Library'] = 'find.Library';
                        $sx .= h('find.adminPlaceBookshelf');
                        $sx .= menu($menu);
                        break;        
                }

            $sx = bs(bsc($sx),12);
            return $sx;
        }
}
