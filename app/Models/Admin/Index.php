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

    function index($d1='',$d2='',$d3='',$d4='',$d5='',$cab='')
        {
            $sx = '';
            switch($d1)
                {
                    case 'Banner':
                            $Logos = new \App\Models\Library\Logos();
                            $sx = $Logos->upload($d1,$d2,$d3,$d4,$d5);
                        break;
                    case 'Logos':
                            $Logos = new \App\Models\Library\Logos();
                            $sx = $Logos->upload($d1,$d2,$d3,$d4,$d5);
                        break;                        
        			case 'Library':
                        $Library = new \App\Models\Library\Libraries();
                        $sx .= $cab;
                        $sx .= $Library->index($d2,$d3,$d4,$d5);
                        break;                        
                    case 'PlaceBookshelf':
                        $PlaceBookshelf = new \App\Models\Bookself\PlaceBookshelf();
                        $sx .= $cab;
                        $sx .= $PlaceBookshelf->index($d2,$d3,$d4);
                        break;
                    default:
                        $sx .= $cab;
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
