<?php

namespace App\Controllers\Find\Rdf;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

helper(['sisdoc','nbr']);

class Concept extends Controller
{
    use ResponseTrait;

    function view_concept($idc=0)
    {
        if ($idc <= 0) {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => 'ID do conceito é obrigatório e deve ser maior que zero'
            ], 400);
        }
        $RDF = new \App\Models\Find\Rdf\RDF();
        $concept = $RDF->le($idc);

        if (!$concept) {
            return $this->respond([
                'status'  => 404,
                'success' => false,
                'message' => 'Conceito não encontrado'
            ], 404);
        }

        return view('find/rdf/concept_view', ['concept' => $concept]);
    }

    function create_concept()
        {
            $RDF = new \App\Models\Find\Rdf\RDF();
            $RDF_Class = new \App\Models\Find\Rdf\RDF_Class();
            $RDF_name = new \App\Models\Find\Rdf\RDF_name();


            $term = $this->request->getPost('term') ?? $this->request->getGet('term') ?? '';
            $Class = $this->request->getPost('class') ?? $this->request->getGet('class') ?? '';

            if ($term != '')
                {
                    if ($Class == 'Person') {
                        $term = nbr_author($term,7);
                    }

                    $RDF->createConcept($Class, $term);
                    return $this->respond([
                            'status'  => 200,
                            'success' => true,
                            'message' => 'Conceito criado com sucesso',
                            'data' => [
                            'Class' => $Class,
                            'Term' => $term
                            ]
                    ]);
            }

            return $this->respond([
                'status'  => 500,
                'success' => true,
                'message' => 'Parametros inválidos term ou class`!'
            ]);
        }

    function add_link_concept()
    {
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();
        $RDF_Class = new \App\Models\Find\Rdf\RDF_Class();

        $idc      = $this->request->getPost('idc') ?? $this->request->getGet('idc');
        $property = $this->request->getPost('property') ?? $this->request->getGet('property');
        $value    = $this->request->getPost('value') ?? $this->request->getGet('value');

        if (is_numeric($property)) {
            $idp = $property;
        } else {
            // Busca o id_c da propriedade pelo nome
            $idp = $RDF_Class->where('c_class', $property)->first();
        }

        if (!$idp) {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => 'Propriedade não encontrada (' . ($property) . ')'
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
                'message' => 'Link já existe',
                'data'    => ['idc' => $idc, 'property' => $property, 'value' => $value]
            ], 400);
        }

        // ❌ validação corrigida
        if (!$idc || !$property || !$value) {
            return $this->respond([
                'status'  => 400,
                'success' => false,
                'message' => "Parâmetros obrigatórios ausentes ($idc, $property, $value)"
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
        $RDF_Data->set($dd)->insert();
        $query = $RDF_Data->getlastquery();

        // ✅ resposta SEMPRE retornada
        return $this->respond([
            'status'  => 200,
            'success' => true,
            'data'    => ['idc' => $idc, 'property' => $property, 'value' => $value],
            'query'   => $query
        ]);
    }
}
