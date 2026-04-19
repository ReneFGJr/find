<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

class Form extends BaseController
{
    public function index($id = null)
    {
        // Buscar conceito e propriedades reais pelo ID
        $rdfModel = new \App\Models\Find\Rdf\RDF();
        $rdfForm = new \App\Models\Find\Rdf\RDF_form();
        $conceptData = $rdfModel->le($id);
        $concept = $conceptData['concept'] ?? [];
        $data = $conceptData['data'] ?? [];

        $Expression = [];
        $Manifestation = [];

        $Work = $rdfForm->getForm('W', null, $id);


        return view('find/rdf/form/index', [
            'concept' => $concept,
            'Work' => $Work,
            'Expression' => $Expression, // Exemplo, ajustar conforme a estrutura real
            'Manifestation' => $Manifestation, // Exemplo, ajustar conforme a estrutura real
            'data' => $data
        ]);
    }
}
