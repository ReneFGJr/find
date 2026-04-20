<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

helper('sisdoc');

class Range_id extends BaseController
{
    public function index()
    {
        $RDF_form_model = new \App\Models\Find\Rdf\RDF_form();
        $RDF_class = new \App\Models\Find\Rdf\RDF_class();

        $Classes = $RDF_class->where('c_type','C')->findAll();

        $id_form = $this->request->getGet('id_form');
        if (!$id_form) {
            return view('find/rdf/range_id', [
                'id_form' => '',
                'range' => null,
                'ClassesMap' => [],
                'ClassesNoMap' => [],
                'error' => 'ID do formulário não informado.'
            ]);
        }
        $data = $RDF_form_model->find($id_form);
        if (!$data) {
            return view('find/rdf/range_id', [
                'id_form' => $id_form,
                'range' => null,
                'ClassesMap' => [],
                'ClassesNoMap' => [],
                'error' => 'Formulário não encontrado.'
            ]);
        }
        $range = $data['form_range'] ?? [];
        $range = is_string($range) ? json_decode($range, true) : $range;

        // Monta ClassesMap (selecionadas) e ClassesNoMap (não selecionadas)
        $ClassesMap = [];
        $ClassesNoMap = [];
        $rangeArr = is_array($range) ? $range : [];
        $rangeArr = array_filter(array_map('strval', $rangeArr));
        foreach ($Classes as $class) {
            $id = (string)$class['id_c'];
            if (in_array($id, $rangeArr)) {
                $ClassesMap[$id] = $class;
            } else {
                $ClassesNoMap[$id] = $class;
            }
        }

        return view('find/rdf/range_id', [
            'id_form' => $id_form,
            'range' => $data['form_range'] ?? null,
            'ClassesMap' => $ClassesMap,
            'ClassesNoMap' => $ClassesNoMap,
            'error' => null
        ]);
    }
}