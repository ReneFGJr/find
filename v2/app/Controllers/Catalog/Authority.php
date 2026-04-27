<?php
namespace App\Controllers\Catalog;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseBuilder;

helper(['sisdoc', 'url', 'cookie']);

class Authority extends BaseController
{
    public function index()
    {
        $selectedId = get_cookie('library') ?: get_cookie('library_code') ?: get_cookie('library_id');
        if (!$selectedId) {
            return redirect()->to('/bibliotecas')->with('msg', 'Escolha uma biblioteca antes de continuar.')->with('msg_type', 'warning');
        }
        $Item = new \App\Models\Find\Items\Index();
        $Authors = $Item->getAuthorsByLibrary($selectedId);

        return view('catalog/authority', ['authors' => $Authors]);
    }

    public function edit($id)
    {
        $selectedId = get_cookie('library') ?: get_cookie('library_code') ?: get_cookie('library_id');
        if (!$selectedId) {
            return redirect()->to('/bibliotecas')->with('msg', 'Escolha uma biblioteca antes de continuar.')->with('msg_type', 'warning');
        }
        $RDF = new \App\Models\Find\Rdf\RDF();
        $RDF->updateRemissive();

        $Authority = $RDF->le($id);
        $AuthorityRemissive = $RDF->getRemissive($id);

        return view('catalog/authority_view', [
            'Authority' => $Authority,
            'AuthorityRemissive' => $AuthorityRemissive
        ]);
    }

    public function save_remissive($id)
    {
        $RDF_Concept = new \App\Models\Find\Rdf\RDF_Concept();
        $use = $this->request->getPost('remissiveSelect');
        $id = $this->request->getPost('id');
        $RDF_Concept->update($use, ['cc_use' => $id]);
        return $this->form_remissive($id);
    }

    public function form_remissive($id_cc='')
    {
        $RDF = new \App\Models\Find\Rdf\RDF();
        $Item = new \App\Models\Find\Items\Index();
        $selectedId = '';

        if ($id_cc == '') {
            $id_cc = get('id_cc');
        }

        if (!$id_cc) {
            return redirect()->back()->with('msg', 'ID de autoridade não fornecido.')->with('msg_type', 'danger');
        }
        $Authority = $RDF->le($id_cc);
        $n_name = $this->request->getGet('initialFilter') ?: $Authority['concept']['name'] ?? '';
        if (!$n_name) {
            $n_name = $Authority['concept']['name'] ?? '';
        }
        $options = [];

        if ($n_name) {
            $Authors = $Item->getAuthorsByLibrary($selectedId, $n_name);
            foreach ($Authors as $author) {
                foreach ($author as $key => $value) {
                    if (is_string($value)) {
                        if ($key != $id_cc) {
                            $options[$key] = $value;
                        }
                    }
                }
            }
        }

        return view('catalog/form_remissive', [
            'initialFilter' => $n_name,
            'options' => $options
        ]);
    }
}
