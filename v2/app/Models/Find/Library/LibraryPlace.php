<?php

namespace App\Models\Find\Library;

use CodeIgniter\Model;

class LibraryPlace extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library_place';
    protected $primaryKey       = 'id_lp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lp_name',
        'lp_address',
        'lp_coord_x',
        'lp_coord_y',
        'lp_email',
        'lp_LIBRARY',
        'lp_contato',
        'lp_responsavel',
        'lp_telefone',
        'lp_site',
        'lp_obs',
        'lp_active',
        'lp_class_type'
    ];

    protected $useTimestamps = false;

    function listByLibrary($libraryCode)
    {
        return $this
            ->where('lp_LIBRARY', $libraryCode)
            ->where('lp_active', 1)
            ->orderBy('lp_name', 'ASC')
            ->findAll();
    }

    function createPlace($data)
    {
        $this->insert($data);
        return ['status' => '200', 'message' => 'Local criado com sucesso', 'id' => $this->getInsertID()];
    }

    function updateName($id, $name)
    {
        $row = $this->find($id);
        if (!$row) {
            return ['status' => '404', 'message' => 'Local não encontrado'];
        }
        $this->update($id, ['lp_name' => $name]);
        return ['status' => '200', 'message' => 'Nome atualizado com sucesso'];
    }
}
