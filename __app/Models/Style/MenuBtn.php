<?php

namespace App\Models\Style;

use CodeIgniter\Model;

class MenuBtn extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'menubtns';
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

    function btnReturn($url)
        {
            $se = '';
            $se .= '<a href="'.$url.'" style="border-bottom: 2px solid #AAA; width: 100%;">';
            $se .= '<div class="mt-4 p-4 btn-outline-danger btn-menu text-center shadow rounded" style="height: 100px; font-size: 150%">';
            $se .= lang('find.return');
            $se .= '</div>';
            $se .= '</a>';
            $sm = bsc($se,6);
            return $sm;
        }

    function menuBtn($its,$url='')
        {
            $sm = '';
			foreach ($its as $label => $link)
				{
					$se = '';
					$se .= '<a href="'.$link.'" style="border-bottom: 2px solid #AAA; width: 100%;">';
					$se .= '<div class="mt-4 p-4 btn-menu btn-outline-primary text-center shadow rounded" style="height: 100px; font-size: 150%">';
					$se .= lang($label);
					$se .= '</div>';
					$se .= '</a>';
					$sm .= bsc($se,6);
				}
                if ($url != '')
                    {
                        $sm .= $this->btnReturn($url);
                    }
				$sx = bs($sm);
                return $sx;            
        }
}
