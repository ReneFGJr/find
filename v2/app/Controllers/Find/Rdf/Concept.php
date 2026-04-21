<?php

namespace App\Controllers\Find\Rdf;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class Concept extends Controller
{
    use ResponseTrait;

    function add_link_concept()
    {
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();
        $RDF_class = new \App\Models\Find\Rdf\RDF_class();

        $idc      = $this->request->getPost('idc');
        $property = $this->request->getPost('property');
        $value    = $this->request->getPost('value');

        $idp = $RDF_class->where('c_class', $property)->first();
        if ($idp) {
            $idp = $idp['id_c'];
        } else {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => 'Propriedade não encontrada'
            ], 400);
        }

        // ❌ validação se ja existe o link (evita duplicidade)
        $existing = $RDF_Data->where([
            'd_r1' => $idc,
            'd_p'  => $idp,
            'd_r2' => $value
        ])->first();
        if ($existing) {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => 'Link já existe'
            ], 400);
        }

        // ❌ validação corrigida
        if (!$idc || !$property || !$value) {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => 'Parâmetros obrigatórios ausentes'
            ], 400);
        }

        // ✅ monta dados
        $dd = [
            'd_r1'      => $idc,
            'd_p'       => $idp,
            'd_r2'      => $value,
            'd_literal' => 0,
            'd_updated' => date('Y-m-d H:i:s'),
            'd_library' => 0,
            'd_use'     => 0
        ];

        // ✅ insere
        $RDF_Data->insert($dd);

        // ✅ resposta SEMPRE retornada
        return $this->respond([
            'status'  => 200,
            'success' => true
        ]);
    }
}
