<?php

namespace App\Models\Library;

use CodeIgniter\Model;

class ItensHistorico extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'itens_historico';
    protected $primaryKey       = 'id_ih';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ih','ih_code','ih_datetime',
        'ih_user','ih_tombo'
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
    
    function add_historicy($id,$code)
        {
            $user = $_SESSION['id'];
            $data = [
                'ih_code' => $code,
                'ih_datetime' => date('Y-m-d H:i:s'),
                'ih_user' => $user,
                'ih_tombo' => $id,
                'ih_library' => LIBRARY
            ];
            $this->insert($data);
        }
    function show_historicy($tombo=0)
        {
            $dt= $this
            ->where('ih_tombo',$tombo)
            ->where('ih_library',LIBRARY)
            ->findAll();

            $sx = '<table class="table table-striped table-bordered table-hover table-sm">';
            for ($r=0;$r < count($dt);$r++)
                {
                    $sx .= '<tr>';
                    $sx .= '<td>';
                    $sx .= $dt[$r]['ih_datetime'];
                    $sx .= '</td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return $sx;
        }
}
