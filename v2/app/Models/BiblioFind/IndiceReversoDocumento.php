<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

class IndiceReversoDocumento extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'indice_reverso_docs';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['d_termo', 'd_doc', 'd_freq'];

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

    public function indexar($termo, $doc, $freq = 1)
    {
        $IR = new IndiceReversoDocumento();
        $IR->where('d_termo', $termo);
        $IR->where('d_doc', $doc);
        $data = $IR->first();

        if (!$data) {
            $data = [
                'd_termo' => $termo,
                'd_doc' => $doc,
                'd_freq' => $freq
            ];
            $IR->insert($data);
        } else {
            if ($data['d_freq'] != $freq) {
                $data = [
                    'd_freq' => $freq
                ];
                $IR->update($IR->id_d, $data);
            }
        }
    }
}
