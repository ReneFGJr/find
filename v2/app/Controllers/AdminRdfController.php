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

    public function edit($id)
    {
        helper(['url', 'form']);
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $model = new RDF_class();
        $record = $model->find($id);
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
            } elseif ($model->where('c_class', $data['c_class'])->where('id_c !=', $id)->first()) {
                $errors[] = 'Já existe um registro com esse nome.';
            } else {
                try {
                    if ($model->update($id, $data)) {
                        return redirect()->to('/admin/rdf-classes')->with('msg', 'Registro RDF atualizado com sucesso.');
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
