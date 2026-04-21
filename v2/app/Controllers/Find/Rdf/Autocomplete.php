<?php

namespace App\Controllers\Find\Rdf;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use App\Models\Find\Rdf\RDF_Concept;

helper("sisdoc");

class Autocomplete extends Controller
{
    use ResponseTrait;

    public function searchConcept()
    {
        $term  = $this->request->getGet('term');
        $range = $this->request->getGet('range');

        // 🔎 Trata range
        if (is_string($range)) {
            $decoded = json_decode($range, true);
            $range = is_array($decoded) ? $decoded : [$range];
        }

        if (!$term || empty($range)) {
            return $this->respond([
                "error" => "Parâmetros obrigatórios ausentes."
            ], 400);
        }

        // 🔎 Normalização
        $term  = strtolower(trim($term));
        $terms = array_filter(explode(" ", $term));

        // ✅ Model
        $model = new RDF_Concept();

        // ✅ Builder
        $builder = $model->builder();

        $builder->select("
            RDF_Concept.id_cc,
            RDF_Concept.cc_class,
            RDF_class.c_class,
            RDF_name.n_name,
            RDF_name.n_lang
        ");

        // 🔗 JOIN com nome preferido
        $builder->join(
            "RDF_name",
            "RDF_name.id_n = RDF_Concept.cc_pref_term",
            "inner"
        );

        // 🔗 ✅ JOIN FALTANTE (corrige erro)
        $builder->join(
            "RDF_class",
            "RDF_class.id_c = RDF_Concept.cc_class",
            "left"
        );

        // 🔥 AND entre termos (precisão)
        foreach ($terms as $t) {
            $builder->like("RDF_name.n_name", $t);
        }

        // 🔥 Prioridade para prefixo
        /*
        $builder->groupStart()
            ->like("RDF_name.n_name", $term, 'after')
            ->orLike("RDF_name.n_name", $term)
            ->groupEnd();
        */

        // 🔎 Filtro de classe
        $builder->whereIn("RDF_Concept.cc_class", $range);

        // 🧠 Ordenação inteligente
        $builder->orderBy("RDF_name.n_name", "ASC");

        // 🚀 Limite
        $builder->limit(20);

        // 🔎 Execução
        $query = $builder->get();
        $RSP   = $query->getResultArray();

        // 🎯 Resultado
        $results = array_map(function ($row) {
            return [
                'id'    => $row['id_cc'],
                'label' => $row['n_name'],
                'lang'  => $row['n_lang'],
                'class' => $row['c_class'] ?? null
            ];
        }, $RSP);

        return $this->respond([
            "status"  => 200,
            "term"    => $term,
            "range"   => $range,
            "results" => $results
        ]);
    }
}
