<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

helper('sisdoc');

class Form_edit extends BaseController
{
    /**
     * Exclui uma propriedade do formulário RDF (AJAX)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function excluir()
    {
        $request = service('request');
        $id = $request->getPost('id');
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID não informado 2.'
            ]);
        }
        $formModel = new \App\Models\Find\Rdf\RDF_form();
        try {
            $formModel->delete($id);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Insere ou atualiza uma propriedade do formulário RDF (AJAX)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function salvar()
    {
        $request = service('request');
        $id_form = $request->getPost('id_form');
        $data = [
            'form_group'           => $request->getPost('form_group'),
            'form_frbr'            => $request->getPost('form_frbr'),
            'form_property'        => $request->getPost('form_property'),
            'form_range'           => $request->getPost('form_range'),
            'form_group_subgroup'  => $request->getPost('form_group_subgroup'),
            'form_library'         => $request->getPost('form_library'),
            'form_order'           => $request->getPost('form_order'),
        ];

        // Validação básica
        if (empty($data['form_frbr']) || empty($data['form_property'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'FRBR e Propriedade são obrigatórios.'
            ]);
        }

        $formModel = new \App\Models\Find\Rdf\RDF_form();
        try {
            if ($id_form) {
                $formModel->update($id_form, $data);
            } else {
                $formModel->insert($data);
            }
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Exibe a view de edição do formulário RDF
     * @return string
     */
    public function index()
    {
        helper(['cookie', 'url']);
        $library = trim((string) (get_cookie('library_code') ?: get_cookie('library')));
        if ($library === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }
        $form = $this->getFormData($library);
        $classModel = new \App\Models\Find\Rdf\RDF_class();

        // Coletar todos os IDs de range e propriedades usados
        $rangeIds = [];
        $propertyIds = [];
        foreach ($form as $f) {
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
            $pid = $f['form_property'] ?? '';
            if ($pid !== '' && !in_array($pid, $propertyIds)) {
                $propertyIds[] = $pid;
            }
        }

        // Buscar nomes das classes
        $classNames = [];
        $classNamesFull = $classModel->whereIn('c_type', ['C'])->orderBy('c_class', 'asc')->findAll();
        foreach ($classNamesFull as $row) {
            $classNames[$row['id_c']] = $row['c_class'];
        }

        $propertyNames = [];
        if (!empty($propertyIds)) {
            $rows = $classModel->whereIn('id_c', $propertyIds)->findAll();
            foreach ($rows as $row) {
                $propertyNames[$row['id_c']] = $row['c_class'];
            }
        }

        // Todas as propriedades para o select
        $allProperties = $classModel->where('c_type', 'P')->orderBy('c_class', 'asc')->findAll();

        // Todas as classes para o painel de edição de Range (apenas c_type = 'C', id => nome)
        $allClassesRows = $classModel->where('c_type', 'C')->orderBy('c_class', 'asc')->findAll();
        $allClasses = [];
        foreach ($allClassesRows as $row) {
            $allClasses[$row['id_c']] = $row['c_class'];
        }

        return view('find/rdf/form/edit', [
            'form' => $form,
            'classNames' => $classNames,
            'propertyNames' => $propertyNames,
            'allProperties' => $allProperties,
            'allClasses' => $allClasses
        ]);
    }

    /**
     * Busca os registros da biblioteca selecionada e os da biblioteca padrão.
     * @return array
     */
    private function getFormData(string $library)
    {
        $formModel = new \App\Models\Find\Rdf\RDF_form();
        $result = $formModel
            ->whereIn('form_library', array_unique(['1000', $library]))
            ->orderBy('form_order', 'asc')
            ->findAll();
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
