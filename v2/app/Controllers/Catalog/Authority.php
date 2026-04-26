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
}
