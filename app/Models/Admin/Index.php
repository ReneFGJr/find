<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library';
    protected $primaryKey       = 'id_l';
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

    function index($id, $ac = '',$d3='',$d4='',$d5='')
        {
            if ($id==0) { $id = LIBRARY; }
            $sa = $this->submenu($id, $ac);
            $sb = $this->config_itens($id, $ac);

            $sx = bsc($sa,3);
            $sx .= bsc($sb,9);
            $sx = bs($sx);
            return $sx;
        }
    function submenu($id, $ac)
    {
        $sx = '';

        $sx .= '<ul class="nav flex-column">';
        $it = array('description', 'places', 'bookshelf', 'logo', 'banners');
        if ($ac == '') {
            $ac = $it[0];
        }

        for ($r = 0; $r < count($it); $r++) {
            if ($it[$r] == $ac) {
                $cl = 'disabled';
            } else {
                $cl = '';
            }
            $sx .= '<li class="mb-2 h5 nav-item text-end"><a href="' . PATH . MODULE . 'admin/' . $id . '/' . $it[$r] . '" class="nav-link ' . $cl . '">' . lang('find.' . $it[$r]) . '</a></li>';
        }
        $sx .= '</ul>';
        return $sx;
    }

    function le_library($id)
        {
            $dt = $this->where('l_code',$id)->findAll();
            if (count($dt) > 0)
                {
                    $dt = $dt[0];
                } 
            return $dt;
        }

    function config_itens($id, $ac = '')
    {
        if ($ac == '') {
            $ac = 'description';
        }
        $sx = h(lang('find.' . $ac),2);
        switch ($ac) {
            default:
                $sx .= 'ERROR';
                break;
            case 'places':
                $id = LIBRARY;
                $sx .= $this->places($id, $ac);
                break;
            case 'bookshelf':
                $id = LIBRARY;
                $sx .= $this->bookshelf($id, $ac);
                break;
            case 'logo':
                $dt = $this->le_library($id);
                if (count($dt) > 0)
                {
                    $sx .= $this->logo($dt);
                    $sx .= '<div class="mt-5">'.lang('find.library').': '.LIBRARY.'</div>';
                } else {
                    $sx .= bsmessage('find.error_library').' #LOGO';
                }
                break;
            case 'banners':
                $sx .= $this->banners($id);
                $sx .= '<div class="mt-5">'.lang('find.library').': '.LIBRARY.'</div>';
                break;
            case 'description':
                $sx .= $this->description($id);
                break;
        }
        return ($sx);
    }
    function description($library,$d3='',$d4='')
    {
        $sx = '';
        $Th = new \App\Models\Library\Libraries(); 
        $dt = $Th->where('l_code',$library)->findAll();
        $id = $dt[0]['id_l'];
        $PlaceBookshelf = new \App\Models\Bookself\PlaceBookshelf();

        $sx .= $Th->edit($id);
        return $sx;
    }

    function places($id)
    {
        $Th = new \App\Models\Library\Places();
        $sx = $Th->view_library($id);
        return $sx;
    }

    function bookshelf($id)
    {
        $Th = new \App\Models\Bookself\PlaceBookshelf();
        $sx = $Th->tableview($id);
        return $sx;
    }

    function logo($id)
    {
        $sx = '';
        $Th = new \App\Models\Library\Logos();

        $sx .= h(lang('find.logo'),4,'mt-5');
        $sx .= $Th->logo($id);
        $sx .= onclick(PATH.MODULE.'popup/logo/logo','800','400');
        $sx .= bsicone('upload');
        $sx .= '</span>';        

        $sx .= h(lang('find.logo'),4,'mt-5');
        $sx .= $Th->logo_mini($id);
        $sx .= onclick(PATH.MODULE.'popup/logo/mini/','800','400');
        $sx .= bsicone('upload');
        $sx .= '</span>';        

        return $sx;
    }
    function banners($id)
    {
        $sx = '';
        $Th = new \App\Models\Library\Logos();        
        for ($r=1;$r <= 3;$r++)
        {
            $sx .= h(lang('find.banner').' #'.$r,4,'mt-5');
            $sx .= '<img src="'.URL.$Th->banner_nr($id,$r).'" width="100%" class="img-fluid p-2 border">';
            $sx .= onclick(PATH.MODULE.'popup/logo/'.$id.'/'.$r,'800','400');
            $sx .= bsicone('upload');
            $sx .= '</span>';
        }        
        return $sx;
    }


    function index2($d1='',$d2='',$d3='',$d4='',$d5='',$cab='')
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
     
                }

            
            return $sx;
        }
}
