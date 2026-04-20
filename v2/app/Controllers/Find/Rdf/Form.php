<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

helper('sisdoc');

class Form extends BaseController
    {
    /**
     * Salva o campo form_range de um formulário RDF
     * Espera POST: id_form, form_range (JSON array)
     */
    public function salvar_range()
        // Debug: mostrar dados antes de salvar
    {
        // Suporte a JSON puro no corpo da requisição
        $data = $this->request->getJSON(true);
        $id_form = $data['id_form'] ?? $this->request->getPost('id_form');
        $form_range = $data['form_range'] ?? $this->request->getPost('form_range');
        // Decodifica se vier como JSON string
        if (is_string($form_range)) {
            $decoded = json_decode($form_range, true);
            if (is_array($decoded)) {
                $form_range = json_encode($decoded); // Salva como JSON string
            }
        }

        // (Removido debug JS para garantir resposta JSON válida)
        $model = new \App\Models\Find\Rdf\RDF_form();
        $msg = '';
        if ($id_form < 1) {
            $msg = 'ID do formulário inválido. ['.$id_form.']';
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        } // Validação extra para ID inválido


        $ok = $model->update($id_form, ['form_range' => $form_range]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao salvar no banco de dados. '.$msg]);
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
        // Pega a biblioteca selecionada do cookie
        if (!function_exists('get_cookie')) {
            helper('cookie');
        }
        $library = get_cookie('library_code') ?: get_cookie('library');

        $Work = $rdfForm->getForm('W', $id, $library);

        return view('find/rdf/form/rdf_edit_concept', [
            'concept' => $concept,
            'Work' => $Work,
            'Expression' => $Expression, // Exemplo, ajustar conforme a estrutura real
            'Manifestation' => $Manifestation, // Exemplo, ajustar conforme a estrutura real
            'data' => $data
        ]);
    }

    public function salvar_literal()
    {
        $data = $this->request->getJSON(true);
        $id_n = $data['id_n'] ?? null;
        $n_name = $data['n_name'] ?? null;
        if (!$id_n || $n_name === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parâmetros obrigatórios não informados.'
            ]);
        }
        $model = new \App\Models\Find\Rdf\RDF_name();

        /************************* Atualização */
        $ok = $model->update($id_n, ['n_name' => $n_name]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar no banco de dados.'
            ]);
        }
    }
}
