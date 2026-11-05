<?php

namespace App\Controllers;

use App\Models\Find\Rdf\RDF_class;

class AdminRdfController extends BaseController
{
    public function index()
    {
        helper('url');
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $model = new RDF_class();
        return view('Admin/rdf_classes', [
            'classes' => $model->where('c_type', 'C')->orderBy('c_class', 'ASC')->findAll(),
            'properties' => $model->where('c_type', 'P')->orderBy('c_class', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        return $this->edit();
    }

    public function edit($id = null)
    {
        helper(['url', 'form']);
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $model = new RDF_class();
        $record = $id === null
            ? ['id_c' => null, 'c_class' => '', 'c_type' => $this->request->getGet('type') === 'P' ? 'P' : 'C']
            : $model->find($id);
        if (!$record) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $errors = [];
        if ($this->request->getMethod() === 'post') {
            $data = [
                'c_class' => trim((string) $this->request->getPost('c_class')),
                'c_type' => (string) $this->request->getPost('c_type'),
            ];
            $validation = \Config\Services::validation();
            $validation->setRules([
                'c_class' => ['label' => 'Nome', 'rules' => 'required|max_length[200]'],
                'c_type' => ['label' => 'Tipo', 'rules' => 'required|in_list[C,P]'],
            ]);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
            } elseif ($model->where('c_class', $data['c_class'])->where('id_c !=', $id ?? 0)->first()) {
                $errors[] = 'Já existe um registro com esse nome.';
            } else {
                try {
                    if ($id === null) {
                        // Campos obrigatórios sem padrão no esquema original.
                        $data += ['c_prefix' => 0, 'c_url' => '', 'c_url_update' => '1900-01-01'];
                    }
                    $saved = $id === null ? $model->insert($data) : $model->update($id, $data);
                    if ($saved !== false) {
                        return redirect()->to('/admin/rdf-classes')->with('msg', $id === null ? 'Registro RDF criado com sucesso.' : 'Registro RDF atualizado com sucesso.');
                    }
                    $errors[] = 'Não foi possível salvar o registro.';
                } catch (\CodeIgniter\Database\Exceptions\DatabaseException $error) {
                    log_message('error', 'Edição RDF: {message}', ['message' => $error->getMessage()]);
                    $errors[] = 'Não foi possível salvar. Verifique se o nome já está em uso.';
                }
            }
            $record = array_merge($record, $data);
        }

        return view('Admin/rdf_class_edit', ['record' => $record, 'errors' => $errors]);
    }
}
