<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Labels extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'labels';
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

    function label($dt)
        {

            $sx = '<div class="text-start" style="padding: 0px 0px 0px 0px; width: 300px; background-color: #f0f0f0; border: 2px solid #000; border-radius: 10px;">';
            for ($r=1;$r <= 4;$r++)
                {                    
                    $bg = '#f0f0ff';
                    $label = $dt['i_ln'.$r];
                    if ($r==4)
                        {
                            $label .= '[cutter]';
                        }
                    $sx .= '<div id="label'.$r.'" style="padding-left: 20px; border-radius: 10px; width: 100%; background-color: '.$bg.'" ><tt>&nbsp;'.$label.'</tt></div>';
                }            
            $sx .= '</div>';
            return $sx;
        }
}
