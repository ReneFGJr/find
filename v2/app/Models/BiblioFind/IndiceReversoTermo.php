<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

class IndiceReversoTermo extends Model
{
    protected $table            = 'indice_reverso_termo';
    protected $primaryKey       = 'id_t';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['t_termo'];

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

    function
    calcula_relevancia($palavra, $frase)
    {
        // Converte tudo para minúsculas para uma comparação insensível a maiúsculas/minúsculas
        $frase = strtolower(ascii($frase));
        $palavra = strtolower(ascii($palavra));

        // Tokenizar a frase em palavras
        $tokens = preg_split('/\W+/', $frase, -1, PREG_SPLIT_NO_EMPTY);

        // Contar as ocorrências da palavra
        $frequencia = array_count_values($tokens);

        $tf = $frequencia[$palavra] ?? 0;

        $tf = $tf + ($tf / strlen($frase));

        return $tf;
    }
}
