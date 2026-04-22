<?php
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela rdf_data
 */
class RDF_Data extends Model
{
    protected $table = 'rdf_data';
    protected $primaryKey = 'id_d';
    protected $allowedFields = [
        'd_r1',
        'd_p',
        'd_r2',
        'd_literal',
        'd_creadted',
        'd_update',
        'd_library',
        'd_user',
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    // Adicione métodos customizados conforme necessário
    function createLink($IDc, $property, $IDd, $literal)
    {
        $RDF_Class = new \App\Models\Find\Rdf\RDF_Class();
        if (!is_numeric($property)) {
            $idp = $RDF_Class->where('c_class', $property)->first();
            if ($idp) {
                $idp = $idp['id_c'];
            } else {
                $idp = null;
            }
        } else {
            $idp = $property;
        }

        if (!$idp) {
            return [
                'status'  => 400,
                'success' => false,
                'message' => 'Propriedade não encontrada'
            ];
        }

        // ❌ validação se ja existe o link (evita duplicidade)
        $existing = $this->where([
            'd_r1' => $IDc,
            'd_p'  => $idp,
            'd_r2' => $IDd,
            'd_literal' => $literal
        ])->first();

        if ($existing) {
            return [
                'status'  => 400,
                'success' => false,
                'message' => 'Link já existe'
            ];
        }



        // ✅ monta dados
        $dd = [
            'd_r1'      => $IDc,
            'd_p'       => $idp,
            'd_r2'      => $IDd,
            'd_literal' => $literal,
            'd_updated' => date('Y-m-d H:i:s'),
            'd_library' => 0,
            'd_use'     => 0
        ];

        // ✅ insere
        $idA = $this->set($dd)->insert();
        return $idA;
    }
}
