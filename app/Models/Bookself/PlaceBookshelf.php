<?php

namespace App\Models\Bookself;

use CodeIgniter\Model;

class PlaceBookshelf extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library_place_bookshelf';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs','bs_name','bs_image','bs_bs','bs_LIBRARY'        
    ];

    protected $typeFields    = [
        'hidden','string:100','string:100','string:100','set:'.LIBRARY        
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

    function index($d1='',$d2='',$d3='',$d4='')
        {
            $this->path = PATH.MODULE.'admin/PlaceBookshelf';
            $this->path_back = PATH.MODULE.'admin/PlaceBookshelf';
            switch($d1)
                {
                    case 'edit':
                        $sx = $this->edit($d2);
                        break;
                    case 'viewid':
                        $sx = $this->viewid($d2);
                        break;
                    default:
                        $this->where('bs_LIBRARY',LIBRARY);
                        $sx = tableview($this);
                        break;
                }
            return $sx;
        }
    function edit($id) 
        {
            $this->id = $id;
            $sx = form($this);
            return $sx;
        }

    function viewid($id)
        {
            $dt = $this->find($id);
            $sx = h($dt['bs_name']);
            return $sx;
        }
}
