<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

helper('sisdoc');

class Form_edit extends BaseController
{

    /**
     * Exclui uma propriedade do formulário RDF (AJAX)
     */
    public function excluir()
    {
        $request = service('request');
        $id = $request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID não informado.']);
        }
        $formModel = new \App\Models\Find\Rdf\RDF_form();
        try {
            $formModel->delete($id);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Salva uma nova propriedade no formulário RDF (AJAX)
     */
    public function salvar()
    {
        $request = service('request');
        $id_form = $request->getPost('id_form');
        // Mapeamento dos campos do formulário para os campos do banco
        $data = [
            'form_group'      => $request->getPost('form_group'),
            'form_frbr'       => $request->getPost('form_frbr'),
            'form_property'   => $request->getPost('form_property'),
            'form_range'      => $request->getPost('form_range'),
            'form_group_subgroup'       => $request->getPost('form_group_subgroup'),
            'form_library'     => $request->getPost('form_library'),
            'form_order'        => $request->getPost('form_order'),
        ];

        // Validação simples
        if (empty($data['form_frbr']) || empty($data['form_property'])) {
            return $this->response->setJSON(['success' => false, 'message' => json_encode($data)]);
        }

        $formModel = new \App\Models\Find\Rdf\RDF_form();
        try {
            if ($id_form) {
                // Atualiza registro existente
                $formModel->update($id_form, $data);
            } else {
                // Cria novo registro
                $formModel->insert($data);
            }
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function index()
    {
        // Exemplo de view de edição de formulário RDF
        // Exemplo: $form deve ser preenchido pelo controller normalmente
        $form = $this->getFormData(); // Supondo função que busca os dados

        // Buscar nomes das classes para todos os IDs de range presentes e propriedades
        $classModel = new \App\Models\Find\Rdf\RDF_Class();
        $rangeIds = [];
        $propertyIds = [];
        foreach ($form as $f) {
            // Ranges
            $ranges = $f['form_range'] ?? '';
            if (is_string($ranges)) {
                $ranges = trim($ranges, '[]');
                $ranges = $ranges ? explode(',', $ranges) : [];
            }
            foreach ($ranges as $rid) {
                $rid = trim($rid);
                if ($rid !== '' && !in_array($rid, $rangeIds)) {
                    $rangeIds[] = $rid;
                }
            }
            // Propriedades
            $pid = $f['form_property'] ?? '';
            if ($pid !== '' && !in_array($pid, $propertyIds)) {
                $propertyIds[] = $pid;
            }
        }
        $classNames = [];
        if (!empty($rangeIds)) {
            $rows = $classModel->whereIn('id_c', $rangeIds)->findAll();
            foreach ($rows as $row) {
                $classNames[$row['id_c']] = $row['c_class'];
            }
        }
        $propertyNames = [];
        if (!empty($propertyIds)) {
            $rows = $classModel->whereIn('id_c', $propertyIds)->findAll();
            foreach ($rows as $row) {
                $propertyNames[$row['id_c']] = $row['c_class'];
            }
        }

        // Buscar todas as propriedades para o select do formulário
        $allProperties = $classModel->orderBy('c_class', 'asc')->findAll();

        return view('find/rdf/form/edit', [
            'form' => $form,
            'classNames' => $classNames,
            'propertyNames' => $propertyNames,
            'allProperties' => $allProperties
        ]);
    }
    /**
     * Busca todos os registros da tabela rdf_form_class_2
     * @return array
     */
    private function getFormData()
    {
        $formModel = new \App\Models\Find\Rdf\RDF_form();
        // Busca todos os registros ordenados por form_order
        $result = $formModel->orderBy('form_order', 'asc')->findAll();
        // Renomeia campos para compatibilidade com a view
        $formData = [];
        foreach ($result as $row) {
            $formData[] = [
                'id_form' => $row['id_form'] ?? '',
                'form_frbr' => $row['form_frbr'] ?? '',
                'form_property' => $row['form_property'] ?? '',
                'form_range' => $row['form_range'] ?? '',
                'form_group' => $row['form_group'] ?? '',
                'form_group_subgroup' => $row['form_group_subgroup'] ?? '',
                'form_library' => $row['form_library'] ?? '',
                'form_order' => $row['form_order'] ?? '',
            ];
        }
        return $formData;
    }
}
