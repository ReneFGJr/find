<?php

namespace App\Models\Social;

use CodeIgniter\Model;

class UsersLibraryModel extends Model
{
    protected $table            = 'users_library';
    protected $primaryKey       = 'id_ul';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array'; // pode ser 'object' se preferir
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'ul_user',
        'ul_library',
        'created_at',
    ];

    // === Configurações de data ===
    protected $useTimestamps = false; // pois created_at é definido por CURRENT_TIMESTAMP
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // não há campo updated_at
    protected $deletedField  = '';

    // === Validações opcionais ===
    protected $validationRules = [
        'ul_user'    => 'required|integer',
        'ul_library' => 'required|integer',
    ];

    protected $validationMessages = [
        'ul_user' => [
            'required' => 'O campo usuário é obrigatório.',
            'integer'  => 'O campo usuário deve ser um número inteiro.'
        ],
        'ul_library' => [
            'required' => 'O campo biblioteca é obrigatório.',
            'integer'  => 'O campo biblioteca deve ser um número inteiro.'
        ],
    ];

    protected $skipValidation = false;
}
