<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Find\Items\Index as ItemModel;
use App\Models\Find\Items\Status as StatusModel;

// Necessário para usar get_cookie() e pre()
helper(['cookie', 'sisdoc']);


class Catalog extends BaseController

{
    // Redireciona catalog/catalogar/phase para catalog/catalogar
    public function phaseRedirect()
    {
        return redirect()->to('/catalog/catalogar');
    }

    // Redireciona catalog/catalogar/metadadoSearch para phase/1
    public function metadadoSearchRedirect()
    {
        return redirect()->to('/catalog/catalogar/phase/1');
    }

    public function rebuildModel()
    {
        $offset = $this->request->getGet('offset') ?? 0;
        $ItemModel = new ItemModel();
        $total = $ItemModel->countAll();
        $limit = 500;

        if ($ItemModel->rebuildAllFields($offset))
            {
                $url = base_url('/catalog/rebuild_fields?offset=') . ($offset + $limit);
                echo view('layout/header', ['title' => 'Rebuild em andamento...']);
                echo '<div class="p-5">';
                echo $url;
                echo '<br>'.date("Y-m-d H:i:s");
                echo '<br>'.$total.'/'.$offset.' registros - limit '.$limit;
                echo '<br>'.round(($offset / $total) * 100, 2).'% processado';
                echo '</div>';
                echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
                exit;
            } else {
                $dd['content'] = 'Rebuild concluído com sucesso!';
                echo view('components/content', $dd);
                exit;
            }
    }

    public function reindexModel()
    {
        $offset = $this->request->getGet('offset') ?? 0;
        $ItemModel = new ItemModel();
        $total = $ItemModel->countAll();
        $limit = 500;
        if ($ItemModel->reindexAll($offset,$limit)) {
            $url = base_url('/catalog/reindex?offset=') . ($offset + $limit);
            echo view('layout/header', ['title' => 'Reindex em andamento...']);
            echo '<div class="p-5">';
            echo $url;
            echo '<br>' . date("Y-m-d H:i:s");
            echo '<br>' . $total . '/' . $offset . ' registros';
            echo '<br>' . round(($offset / $total) * 100, 2) . '% processado';
            echo '</div>';
            echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
            exit;
        } else {
           $dd['content'] = 'Reindex concluído com sucesso!';
           echo view('components/content', $dd);
           exit;
        }
    }

    public function checkerModel()
    {
        $CheckerModel = new \App\Models\Find\Check\CheckerModel();
        return $CheckerModel->checkFindItem();
    }

    private function checkCatalogPermission()
    {
        $session = session();
        $userId = $session->get('id_us');
        if (!$userId) {
            return false;
        }
        $db = \Config\Database::connect();
        $builder = $db->table('users_group_members m')
            ->select('m.*')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'inner')
            ->where('m.grm_user', $userId)
            ->where('(g.gr_hash = "#ADM" or g.gr_hash = "#CAT")')
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)');
        $result = $builder->get()->getFirstRow();
        return $result ? true : false;
    }

    private function denyIfNoPermission()
    {
        if (!$this->checkCatalogPermission()) {
            return redirect()->to('/login')->with('msg', 'Acesso restrito. Apenas administradores e catalogadores podem acessar.');
        }
    }

    public function index()
    {
        if ($resp = $this->denyIfNoPermission()) return $resp;
        // Exemplo: Resumo dos itens por status
        $itemModel = new ItemModel();
        $itemModel->set(['i_status' => 1])->where('i_status',0)->update();

        $statusModel = new StatusModel();
        // Obtém o código da biblioteca (GET ou cookie, conforme padrão do sistema)
        $library = $this->request->getGet('library');
        if (!$library) {
            $library = get_cookie('library_code') ?? get_cookie('library') ?? '';
        }
        $statusResumo = [];
        if ($library) {
            $resumo = $itemModel->select('i_status, COUNT(*) as qtd')
                ->where('i_library', $library)
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
                    'id' => $row['i_status'],
                    'status' => $nome,
                    'qtd' => $row['qtd']
                ];
            }
        }
        return view('catalog/index', [
            'statusResumo' => $statusResumo,
            'library' => $library
        ]);
    }
    public function catalogar()
    {
        if ($resp = $this->denyIfNoPermission()) return $resp;
        $itemModel = new ItemModel();
        $statusModel = new StatusModel();
        $library = $this->request->getGet('library');
        if (!$library) {
            $library = get_cookie('library_code') ?? get_cookie('library') ?? '';
        }
        $statusResumo = [];
        if ($library) {
            $resumo = $itemModel->select('i_status, COUNT(*) as qtd')
                ->where('i_library', $library)
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
                    'id' => $row['i_status'],
                    'status' => $nome,
                    'qtd' => $row['qtd']
                ];
            }
        }
        return view('catalog/catalogar', [
            'statusResumo' => $statusResumo,
            'library' => $library
        ]);
    }

    public function metadadoSearch($IdItem = null)
    {
        $cover_result = null;
        if ($resp = $this->denyIfNoPermission()) return $resp;
        $resultados = null;
        $busca = $this->request->getGet('busca');
        $itemInfo = null;
        if ($IdItem) {
            $itemModel = new ItemModel();
            $itemInfo = $itemModel->find($IdItem);
        }
        $isbn = $itemInfo['i_identifier'] ?? $this->request->getPost('isbn') ?? null;

        /**************************************************** Cover */
        // Se for POST e import_z39_50=1, mostra view de loading e dispara consulta
        if ($this->request->getMethod() === 'post' && $this->request->getPost('import_cover') == 1) {
            // Exibe view auxiliar de loading
            $cover_result = view('catalog/cover_loading', ['isbn' => $isbn]);
            //echo searchGoogle($isbn);
            // Busca ISBN do item (se disponível)
        }

        // Se for POST e import_z39_50=1, mostra view de loading e dispara consulta
        if ($this->request->getMethod() === 'post' && $this->request->getPost('import_z39_50') == 1) {
            // Exibe view auxiliar de loading
            echo view('catalog/z3950_loading');
            // Busca ISBN do item (se disponível)

            if ($isbn) {
                // Chama o model Z3950
                $z3950 = new \App\Models\Z3950\Index();
                $resultadoZ39 = $z3950->searchISBN($isbn);
                // Aqui você pode salvar o resultado em sessão, banco ou redirecionar para mostrar o resultado
                session()->setFlashdata('z3950_result', $resultadoZ39);
                return redirect()->to(current_url());
            }
            // Se não houver ISBN, apenas retorna
            return;
        }

        // Se houver resultado Z39 salvo, passa para a view
        $z3950_result = session()->getFlashdata('z3950_result');
        if (isset($z3950_result)) {
            $ProcessMetadata = new \App\Models\Find\Items\ProcessMetadata();
            $z3950_result = $ProcessMetadata->processZ3950Result($z3950_result,$isbn);
            //return redirect()->to(current_url());
        }

        if ($busca) {
            // Exemplo: simulação de busca, substitua por consulta real
            $resultados = [
                ['titulo' => 'Livro Exemplo 1', 'autor' => 'Autor A', 'isbn' => '1234567890123'],
                ['titulo' => 'Livro Exemplo 2', 'autor' => 'Autor B', 'isbn' => '9876543210987']
            ];
        }
        return view('catalog/metadadoSearch', [
            'resultados' => $resultados,
            'itemInfo' => $itemInfo,
            'idItem' => $IdItem,
            'busca' => $busca,
            'z3950_result' => $z3950_result,
            'cover_result' => $cover_result
        ]);
    }

    public function catalogar_phase($status = null)
    {
        if ($resp = $this->denyIfNoPermission()) return $resp;
        $libraryCode = get_cookie('library_code') ?? get_cookie('library') ?? '';
        $obras = [];
        $busca = $this->request->getGet('busca');
        if ($libraryCode && $status !== null) {
            $itemModel = new ItemModel();
            $builder = $itemModel
                ->where('i_library', $libraryCode)
                ->where('i_status', $status);
            if ($status == 5) {
                if ($busca) {
                    $builder = $builder
                        ->groupStart()
                        ->like('i_tombo', $busca)
                        ->orLike('i_titulo', $busca)
                        ->orLike('i_autores', $busca)
                        ->orLike('i_identifier', $busca)
                        ->groupEnd();
                }
                $obras = $builder->orderBy('i_created', 'DESC')->findAll(50);
            } else {
                $obras = $builder->orderBy('i_created', 'DESC')->findAll();
            }
        }
        return view('catalog/phase', [
            'status' => $status,
            'obras' => $obras,
            'busca' => $busca
        ]);
    }

    public function excluir_exemplar()
    {
        if ($resp = $this->denyIfNoPermission()) return $resp;
        if ($this->request->getMethod() === 'post') {
            $id = $this->request->getPost('id');
            if ($id) {
                $itemModel = new ItemModel();
                $item = $itemModel->find($id);
                if ($item && isset($item['i_status']) && $item['i_status'] < 5) {
                    $itemModel->delete($id);
                    return redirect()->back()->with('msg', 'Exemplar excluído com sucesso!');
                }
            }
            return redirect()->back()->with('msg', 'Não foi possível excluir o exemplar.');
        }
        return redirect()->back();
    }

    public function catalogar_isbn()
    {
        if ($resp = $this->denyIfNoPermission()) return $resp;
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
                    'id' => $row['i_status'],
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
                $msgRedirect = '';
                if ($item) {
                    $titulo = isset($item['i_titulo']) && $item['i_titulo'] !== null ? $item['i_titulo'] : '';
                    $exemplar = isset($item['i_exemplar']) && $item['i_exemplar'] ? ((int)$item['i_exemplar']) + 1 : 1;
                    // Se não veio o GET 'confirmar', perguntar ao usuário
                    if (!$this->request->getPost('confirmar')) {
                        $msg = '<div class="alert alert-warning">Livro já cadastrado na base local: ' . htmlspecialchars($titulo) . '.<br>Deseja incluir um novo exemplar?<br>';
                        $msg .= '<div class="d-flex gap-2 mt-2">';
                        $msg .= '<form method="post">';
                        $msg .= '<input type="hidden" name="isbn" value="' . htmlspecialchars($isbn) . '">';
                        $msg .= '<input type="hidden" name="patrimonio" value="' . htmlspecialchars($patrimonio) . '">';
                        $msg .= '<input type="hidden" name="auto_gerar" value="' . ($auto_gerar ? '1' : '') . '">';
                        $msg .= '<input type="hidden" name="local" value="' . htmlspecialchars($local) . '">';
                        $msg .= '<input type="hidden" name="confirmar" value="1">';
                        $msg .= '<button type="submit" class="btn btn-sm btn-primary">Sim, incluir novo exemplar</button>';
                        $msg .= '</form>';
                        $msg .= '<form method="get">';
                        $msg .= '<input type="hidden" name="isbn" value="' . htmlspecialchars($isbn) . '">';
                        $msg .= '<button type="submit" class="btn btn-sm btn-secondary">Não, voltar</button>';
                        $msg .= '</form>';
                        $msg .= '</div>';
                        $msg .= '</div>';
                        return view('catalog/isbn', [
                            'msg' => $msg,
                            'isbn' => $isbn,
                            'item' => $item,
                            'patrimonio' => '',
                            'auto_gerar' => true,
                            'local' => '',
                            'places' => $places,
                            'statusResumo' => $statusResumo
                        ]);
                    }
                    $msgRedirect = 'Novo exemplar cadastrado para o livro: ' . $titulo;
                } else {
                    $msgRedirect = 'Livro cadastrado com sucesso na biblioteca!';
                }
                // Só insere se não existe ou se confirmou
                if (!$item || $this->request->getPost('confirmar')) {
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
                // Redirect para zerar o formulário
                return redirect()->to(current_url() . '?msg=' . urlencode($msgRedirect));
            } else {
                $msg = '<div class="alert alert-danger">Informe um ISBN-13 válido.</div>';
            }
        } else {
            $auto_gerar = true; // default checked
        }
        // Mensagem de sucesso por GET
        if (empty($msg) && $this->request->getGet('msg')) {
            $msg = '<div class="alert alert-success">' . htmlspecialchars($this->request->getGet('msg')) . '</div>';
        }
        return view('catalog/isbn', [
            'msg' => $msg,
            'isbn' => '',
            'item' => null,
            'patrimonio' => '',
            'auto_gerar' => true,
            'local' => '',
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

    public function util()
    {
        // Exemplo de view simples para utilitários
        return view('catalog/util');
    }

    public function no_action($id = null)
    {
        return view('catalog/no_action', ['id' => $id]);
    }
}
