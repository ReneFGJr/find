<?php

namespace App\Models\Find\Library;

use CodeIgniter\Model;

class LibraryRede extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library_rede';
    protected $primaryKey       = 'id_rd';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rd',
        'rd_name',
        'rd_descricao',
        'rd_obs',
        'lr_active',
    ];

    protected $useTimestamps = false;

    // Métodos utilitários
    public function listAllActive()
    {
        // Older databases do not have an activation field for networks.
        if ($this->db->fieldExists('rd_active', $this->table)) {
            $this->where('rd_active', 1);
        } elseif ($this->db->fieldExists('lr_active', $this->table)) {
            $this->where('lr_active', 1);
        }

        return $this->orderBy('rd_name', 'ASC')->findAll();
    }

    public function getByName($name)
    {
        return $this->where('rd_name', $name)->first();
    }
}
