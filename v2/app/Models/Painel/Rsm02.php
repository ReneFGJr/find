<?php

namespace App\Models\Painel;

use CodeIgniter\Model;

class Rsm02 extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rsm01s';
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

    function index($d1='',$d2='',$d3='')
        {
            $sx = '';
            $sx .= '<h5>BIBLIOTECAS</h5>';
            $sx .= 'Bibliotecas: <b>0</b><br>';
            $sx .= 'Cidades: <b>0</b><br>';

            return $sx;
        }
}
