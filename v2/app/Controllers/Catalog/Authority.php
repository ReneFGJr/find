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
}
