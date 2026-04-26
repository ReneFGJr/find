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

    public function getCutterFullName($d2 = '', $d3 = '', $d4 = '')
    {
        if ($d2 != '') {

            $name = nbr_author($d2,1);
            if (strpos($name, ',') !== false) {
                //$name = substr($name, 0, strpos($name, ',')); // Pega apenas o sobrenome antes da vírgula
            }
            $cutterArr = $this->getCutterBySurname($name);
            $cutterCode = $cutterArr ? substr($name,0,1).$cutterArr['cutter_code'] : null;
            return ['cutter_code' => $cutterCode, 'author_name' => $name];
        }
        return ['error' => 'Nome completo não fornecido'];
    }

    /**
     * Busca o Cutter mais próximo para o sobrenome informado
     */
    public function getCutterBySurname(string $surname): ?array
    {
        $surname = ucfirst(strtolower(trim($surname)));

        $RSP = $this->where('cutter_abrev <=', $surname)
            ->orderBy('cutter_abrev', 'DESC')
            ->first();
        if ($RSP) {
            return $RSP;
        } else {
            return null;
        }
    }
}
