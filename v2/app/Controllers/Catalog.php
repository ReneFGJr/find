<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Find\Items\Index as ItemModel;
use App\Models\Find\Items\Status as StatusModel;

// Necessário para usar get_cookie()
helper('cookie');


class Catalog extends BaseController
{
    public function index()
    {
        // Exemplo: Resumo dos itens por status
        $itemModel = new ItemModel();
        $statusModel = new StatusModel();
        // Obtém o código da biblioteca (GET ou cookie, conforme padrão do sistema)
        $library = $this->request->getGet('library');
        if (!$library) {
            $library = get_cookie('library_code') ?? get_cookie('library') ?? '';
        }
        $resumo = [];
        if ($library) {
            $resumo = $itemModel->select('i_status, COUNT(*) as qtd')
                ->where('i_library', $library)
                ->groupBy('i_status')
                ->findAll();
        }
        $statusList = $statusModel->findAll();
        $statusNames = [];
        foreach ($statusList as $status) {
            $statusNames[$status['id_is']] = $status['is_name'];
        }
        $resumoArr = [];
        foreach ($resumo as $row) {
            $nome = $statusNames[$row['i_status']] ?? 'Status #' . $row['i_status'];
            $resumoArr[$nome] = $row['qtd'];
        }
        return view('catalog/index', [
            'resumo' => $resumoArr,
            'library' => $library
        ]);
    }
    public function catalogar()
    {
        return view('catalog/catalogar');
    }
    public function catalogar_isbn()
    {
        $msg = '';
        $isbn = '';
        $item = null;
        $patrimonio = '';
        $auto_gerar = true;
        $local = '';

        // Buscar locais de catalogação (todas as bibliotecas ou da biblioteca atual)
        $libraryCode = get_cookie('library_code') ?? get_cookie('library') ?? '';
        $places = [];
        $statusResumo = [];
        if ($libraryCode) {
            $placeModel = new \App\Models\Find\Library\LibraryPlace();
            $places = $placeModel->listByLibrary($libraryCode);

            // Resumo dos status dos itens catalogados na biblioteca
            $itemModel = new ItemModel();
            $statusModel = new StatusModel();
            $resumo = $itemModel->select('i_status, COUNT(*) as qtd')
                ->where('i_library', $libraryCode)
                ->groupBy('i_status')
                ->findAll();
            $statusList = $statusModel->findAll();
            $statusNames = [];
            foreach ($statusList as $status) {
                $statusNames[$status['id_is']] = $status['is_name'];
            }
            foreach ($resumo as $row) {
                $nome = $statusNames[$row['i_status']] ?? 'Status #' . $row['i_status'];
                $statusResumo[] = [
                    'status' => $nome,
                    'qtd' => $row['qtd']
                ];
            }
        }

        if ($this->request->getMethod() === 'post') {
            $isbn = preg_replace('/[^0-9Xx]/', '', strtoupper($this->request->getPost('isbn')));
            $patrimonio = $this->request->getPost('patrimonio');
            $auto_gerar = $this->request->getPost('auto_gerar') !== null ? true : false;
            $local = $this->request->getPost('local');

            // Funções auxiliares para ISBN
            $isbn = $this->normalizeIsbn($isbn);
            if ($isbn && $this->isValidIsbn13($isbn)) {
                $itemModel = new ItemModel();
                // Busca itens desse ISBN na biblioteca atual
                $item = $itemModel->where('i_identifier', $isbn)->where('i_library', $libraryCode)->orderBy('i_exemplar', 'desc')->first();
                $tombo = $auto_gerar ? $itemModel->nextTombo($libraryCode) : $patrimonio;
                $exemplar = 1;
                if ($item) {
                    $titulo = isset($item['i_titulo']) && $item['i_titulo'] !== null ? $item['i_titulo'] : '';
                    $exemplar = isset($item['i_exemplar']) && $item['i_exemplar'] ? ((int)$item['i_exemplar']) + 1 : 1;
                    $msg = '<div class="alert alert-success">Livro já cadastrado na base local: <b>' . htmlspecialchars($titulo) . '</b></div>';
                } else {
                    $msg = '<div class="alert alert-success">Livro cadastrado com sucesso na biblioteca!</div>';
                }
                // Sempre insere novo exemplar
                if (!$item || $exemplar > 1) {
                    $data = [
                        'i_identifier' => $isbn,
                        'i_library' => $libraryCode,
                        'i_library_place' => $local,
                        'i_tombo' => $tombo,
                        'i_created' => date('Y-m-d H:i:s'),
                        'i_status' => 1,
                        'i_exemplar' => $exemplar,
                    ]; // Adicione outros campos conforme necessário
                    $itemModel->insert($data);
                }
                // Passa info para a view
                $item = [
                    'i_titulo' => $item['i_titulo'] ?? '',
                    'i_identifier' => $isbn,
                    'i_tombo' => $tombo,
                    'i_exemplar' => $exemplar
                ];
            } else {
                $msg = '<div class="alert alert-danger">Informe um ISBN-13 válido.</div>';
            }
        } else {
            $auto_gerar = true; // default checked
        }
        return view('catalog/isbn', [
            'msg' => $msg,
            'isbn' => $isbn,
            'item' => $item,
            'patrimonio' => $patrimonio,
            'auto_gerar' => $auto_gerar,
            'local' => $local,
            'places' => $places,
            'statusResumo' => $statusResumo
        ]);
    }

    // Normaliza ISBN: converte ISBN-10 para ISBN-13 se necessário
    private function normalizeIsbn($isbn)
    {
        if (strlen($isbn) == 13) {
            return $isbn;
        }
        if (strlen($isbn) == 10) {
            // Converter ISBN-10 para ISBN-13
            $isbn13 = '978' . substr($isbn, 0, 9);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$isbn13[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            $check = (10 - ($sum % 10)) % 10;
            return $isbn13 . $check;
        }
        return '';
    }

    // Valida ISBN-13
    private function isValidIsbn13($isbn)
    {
        if (strlen($isbn) != 13 || !ctype_digit($isbn)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check == (int)$isbn[12];
    }
}
