<?php

namespace App\Models;

use CodeIgniter\Model;

class CutterModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table      = 'cutter';
    protected $primaryKey = 'id_cutter';
    protected $allowedFields = ['cutter_code', 'cutter_abrev'];

    function cutter_code(string $fullname): ?string
    {
        $parts = explode(' ', trim($fullname));
        $surname = end($parts); // pega o último nome como sobrenome

        $cutter = $this->getCutterBySurname($surname);

        return $cutter ? $cutter['cutter_code'] : null;
    }

    /**
     * Busca o Cutter mais próximo para o sobrenome informado
     */
    public function getCutterBySurname(string $surname): ?array
    {
        $surname = ucfirst(strtolower(trim($surname)));

        return $this->where('cutter_abrev <=', $surname)
            ->orderBy('cutter_abrev', 'DESC')
            ->first();
    }
}
