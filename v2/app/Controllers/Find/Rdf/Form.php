<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

class Form extends BaseController
    {
    /**
     * Salva o campo form_range de um formulário RDF
     * Espera POST: id_form, form_range (JSON array)
     */
    public function salvar_range()
    {
        $id_form = $this->request->getPost('id_form');
        $form_range = $this->request->getPost('form_range');
        // Decodifica se vier como JSON string
        if (is_string($form_range)) {
            $decoded = json_decode($form_range, true);
            if (is_array($decoded)) {
                $form_range = json_encode($decoded); // Salva como JSON string
            }
        }
        $model = new \App\Models\Find\Rdf\RDF_form();
        $ok = $model->update($id_form, ['form_range' => $form_range]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao salvar no banco de dados.']);
        }
    }

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
