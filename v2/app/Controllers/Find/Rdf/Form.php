<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

class Form extends BaseController
{
    public function index($id = null)
    {
        // Buscar conceito e propriedades reais pelo ID
        $rdfModel = new \App\Models\Find\Rdf\RDF();
        $conceptData = $rdfModel->le($id);
        $concept = $conceptData['concept'] ?? [];
        $data = $conceptData['data'] ?? [];
        return view('find/rdf/form/index', [
            'concept' => $concept,
            'data' => $data
        ]);
    }
}
