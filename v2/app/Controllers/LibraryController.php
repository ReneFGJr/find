<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Find\Library\Index as LibraryIndex;
use App\Models\Find\Items\Index as ItemsIndex;

class LibraryController extends BaseController
{
    public function bibliotecas()
    {
        helper(['url', 'cookie']);

        $model = new LibraryIndex();
        $libraries = $model->listAll();
        $selectedId = get_cookie('library') ?: get_cookie('library_code') ?: get_cookie('library_id');

        return view('Libraries/index', [
            'libraries' => $libraries,
            'selectedId' => $selectedId,
        ]);
    }

    public function select()
    {
        helper(['url', 'cookie']);

        $selection = trim((string) $this->request->getPost('library_id'));
        if ($selection === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $model = new LibraryIndex();
        $library = $model->getSelectedLibrary($selection);

        if (!$library) {
            return redirect()->to('/bibliotecas')->with('msg', 'Biblioteca não encontrada.')->with('msg_type', 'danger');
        }

        $response = redirect()->to('/library')->with('msg', 'Biblioteca selecionada com sucesso.')->with('msg_type', 'success');
        $expire = 60 * 60 * 24 * 30;
        $response->setCookie('library', (string) ($library['code'] ?? ''), $expire);
        $response->setCookie('library_code', (string) ($library['code'] ?? ''), $expire);
        $response->setCookie('library_id', (string) ($library['id'] ?? ''), $expire);
        $response->setCookie('library_name', rawurlencode((string) ($library['name'] ?? '')), $expire);

        return $response;
    }

    public function library()
    {
        helper(['url', 'cookie']);

        $cookieId = trim((string) (get_cookie('library_id') ?? ''));
        $cookieCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));

        if ($cookieId === '' && $cookieCode === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Escolha uma biblioteca antes de continuar.')->with('msg_type', 'warning');
        }

        $model = new LibraryIndex();
        $library = null;

        if ($cookieCode !== '') {
            $library = $model->getSelectedLibrary($cookieCode);
        }

        if (!$library && $cookieId !== '') {
            $library = $model->getSelectedLibrary($cookieId);
        }

        if (!$library) {
            return redirect()->to('/bibliotecas')->with('msg', 'A biblioteca salva no cookie não foi localizada.')->with('msg_type', 'warning');
        }

        $itemsModel = new ItemsIndex();
        $vitrine = $itemsModel->vitrine($library['code']);

        return view('Libraries/library', [
            'library' => $library,
            'cookieId' => $cookieId !== '' ? $cookieId : $cookieCode,
            'vitrine' => $vitrine,
        ]);
    }

    public function item($id)
    {
        helper(['url', 'cookie']);

        $cookieCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        if ($cookieCode === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Escolha uma biblioteca antes de continuar.')->with('msg_type', 'warning');
        }

        $itemsModel = new ItemsIndex();
        $row = $itemsModel->where('id_i', $id)->first();

        if (!$row) {
            return redirect()->to('/library')->with('msg', 'Item não encontrado.')->with('msg_type', 'danger');
        }

        $isbn = $row['i_identifier'];
        $lib = $row['i_library'];
        $book = $itemsModel->getISBN($isbn, $lib);

        if (empty($book)) {
            return redirect()->to('/library')->with('msg', 'Item não encontrado.')->with('msg_type', 'danger');
        }

        $libModel = new LibraryIndex();
        $library = $libModel->getSelectedLibrary((string) $lib);

        return view('Libraries/item', [
            'book' => $book,
            'library' => $library,
        ]);
    }
}
