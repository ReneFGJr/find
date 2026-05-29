<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Find\Items\Historic as ItemHistoric;
use App\Models\User\UserLibrary;

helper('cookie');

class Emprestimo extends BaseController
{
    private function loanSessionKey(int $userId, string $libraryCode): string
    {
        return 'loan_' . $libraryCode . '_' . $userId;
    }

    private function getLoanCart(int $userId, string $libraryCode): array
    {
        return (array) (session()->get($this->loanSessionKey($userId, $libraryCode)) ?? []);
    }

    private function setLoanCart(int $userId, string $libraryCode, array $cart): void
    {
        session()->set($this->loanSessionKey($userId, $libraryCode), array_values($cart));
    }

    private function clearLoanCart(int $userId, string $libraryCode): void
    {
        session()->remove($this->loanSessionKey($userId, $libraryCode));
    }

    private function itemStatusLabel(int $status): string
    {
        $map = [
            1 => 'Disponível',
            2 => 'Reservado',
            3 => 'Em processamento',
            4 => 'Em restauração',
            5 => 'Indisponível',
            6 => 'Emprestado',
        ];

        return $map[$status] ?? 'Status desconhecido';
    }

    private function getItemByTombo(string $libraryCode, int $tombo): ?array
    {
        $db = \Config\Database::connect();
        $row = $db->table('find_item')
            ->select('id_i, i_tombo, i_titulo, i_identifier, i_status, i_library, i_library_place, i_usuario, i_dt_prev')
            ->where('i_library', $libraryCode)
            ->where('i_tombo', $tombo)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public function index()
    {
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        $libraryCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        if ($libraryCode === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $libraryModel = new \App\Models\Find\Library\Index();
        $library = $libraryModel->getSelectedLibrary($libraryCode);
        $libraryId = (int) ($library['id'] ?? 0);

        $db = \Config\Database::connect();
        $groupRows = $db->table('users_group_members m')
            ->select('m.grm_user AS id_us, g.gr_name')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'left')
            ->where('m.grm_library', $libraryCode)
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)')
            ->get()
            ->getResultArray();

        $groupsByUser = [];
        foreach ($groupRows as $row) {
            $uid = (int) ($row['id_us'] ?? 0);
            $gname = trim((string) ($row['gr_name'] ?? ''));
            if ($uid <= 0 || $gname === '') {
                continue;
            }
            if (!isset($groupsByUser[$uid])) {
                $groupsByUser[$uid] = [];
            }
            if (!in_array($gname, $groupsByUser[$uid], true)) {
                $groupsByUser[$uid][] = $gname;
            }
        }

        $linkedIds = [];
        if ($libraryId > 0) {
            $linkedRows = $db->table('users_library')
                ->select('ul_user')
                ->where('ul_library', $libraryId)
                ->get()
                ->getResultArray();

            foreach ($linkedRows as $lrow) {
                $uid = (int) ($lrow['ul_user'] ?? 0);
                if ($uid > 0) {
                    $linkedIds[$uid] = true;
                }
            }
        }

        if ($q !== '') {
            // Busca global no cadastro quando há termo de pesquisa.
            $userRows = $db->table('users u')
                ->select('u.id_us, u.us_nome, u.us_email')
                ->groupStart()
                    ->like('u.us_nome', $q)
                    ->orLike('u.us_email', $q)
                ->groupEnd()
                ->orderBy('u.us_nome', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            // Sem termo, lista apenas usuários já vinculados à biblioteca selecionada.
            $userRows = $db->table('users u')
                ->select('u.id_us, u.us_nome, u.us_email')
                ->join('users_library ul', 'ul.ul_user = u.id_us', 'inner')
                ->where('ul.ul_library', $libraryId)
                ->orderBy('u.us_nome', 'ASC')
                ->get()
                ->getResultArray();
        }

        $usersMap = [];
        foreach ($userRows as $row) {
            $id = (int) ($row['id_us'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $usersMap[$id] = [
                'id_us' => $id,
                'us_nome' => (string) ($row['us_nome'] ?? ''),
                'us_email' => (string) ($row['us_email'] ?? ''),
                'grupos' => $groupsByUser[$id] ?? [],
                'vinculado' => isset($linkedIds[$id]),
            ];
        }

        $users = array_values($usersMap);

        return view('Emprestimo/index', [
            'users' => $users,
            'library' => $library,
            'libraryCode' => $libraryCode,
            'libraryId' => $libraryId,
            'q' => $q,
        ]);
    }

    public function bindLibrary()
    {
        $q = trim((string) ($this->request->getPost('q') ?? ''));
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        $userId = (int) ($this->request->getPost('id_us') ?? 0);
        if ($userId <= 0) {
            return redirect()->back()->with('msg', 'Usuário inválido para vinculação.')->with('msg_type', 'warning');
        }

        $libraryCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        $libraryModel = new \App\Models\Find\Library\Index();
        $library = $libraryModel->getSelectedLibrary($libraryCode);
        $libraryId = (int) ($library['id'] ?? 0);

        if ($libraryId <= 0) {
            return redirect()->back()->with('msg', 'Biblioteca não identificada para vinculação.')->with('msg_type', 'warning');
        }

        $userLibrary = new UserLibrary();
        $status = $userLibrary->updateUserLibrary($userId, $libraryId);

        $redirect = '/emprestimo';
        if ($q !== '') {
            $redirect .= '?q=' . urlencode($q);
        }

        if ($status === 200 || $status === 100) {
            return redirect()->to($redirect)->with('msg', 'Usuário vinculado à biblioteca com sucesso.')->with('msg_type', 'success');
        }

        return redirect()->to($redirect)->with('msg', 'Não foi possível vincular o usuário à biblioteca.')->with('msg_type', 'danger');
    }

    public function unbindLibrary()
    {
        $q = trim((string) ($this->request->getPost('q') ?? ''));
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        $userId = (int) ($this->request->getPost('id_us') ?? 0);
        if ($userId <= 0) {
            return redirect()->back()->with('msg', 'Usuário inválido para remoção de vínculo.')->with('msg_type', 'warning');
        }

        $libraryCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        $libraryModel = new \App\Models\Find\Library\Index();
        $library = $libraryModel->getSelectedLibrary($libraryCode);
        $libraryId = (int) ($library['id'] ?? 0);

        if ($libraryId <= 0) {
            return redirect()->back()->with('msg', 'Biblioteca não identificada para remoção de vínculo.')->with('msg_type', 'warning');
        }

        $userLibrary = new UserLibrary();
        $userLibrary
            ->where('ul_user', $userId)
            ->where('ul_library', $libraryId)
            ->delete();

        $redirect = '/emprestimo';
        if ($q !== '') {
            $redirect .= '?q=' . urlencode($q);
        }

        return redirect()->to($redirect)->with('msg', 'Vínculo removido com sucesso.')->with('msg_type', 'success');
    }

    private function hasAdminPermission(): bool
    {
        $userId = (int) (session()->get('id_us') ?? 0);
        if ($userId <= 0) {
            return false;
        }

        $library = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));

        $db = \Config\Database::connect();
        $builder = $db->table('users_group_members m')
            ->select('g.gr_name, g.gr_hash')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'left')
            ->where('m.grm_user', $userId)
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)');

        if ($library !== '') {
            $builder->where('m.grm_library', $library);
        }

        $rows = $builder->get()->getResultArray();

        foreach ($rows as $row) {
            $groupName = strtolower(trim((string) ($row['gr_name'] ?? '')));
            $groupHash = strtoupper(trim((string) ($row['gr_hash'] ?? '')));

            if ($groupHash === '#ADM' || $groupName === 'administrador' || $groupName === 'admin') {
                return true;
            }
        }

        return false;
    }

    private function denyIfNoPermission()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('msg', 'Faca login para acessar.')->with('msg_type', 'warning');
        }

        if (!$this->hasAdminPermission()) {
            return redirect()->to('/')->with('msg', 'Acesso restrito. Apenas administradores podem acessar Emprestimo.')->with('msg_type', 'warning');
        }

        return null;
    }

    public function rdf_concept_add()
    {
        return $this->index();
    }

    public function relatorio()
    {
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        $libraryCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        if ($libraryCode === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $libraryModel = new \App\Models\Find\Library\Index();
        $library = $libraryModel->getSelectedLibrary($libraryCode);
        $libraryId = (int) ($library['id'] ?? 0);
        $historyLibraryId = is_numeric($libraryCode) ? (int) $libraryCode : $libraryId;

        $db = \Config\Database::connect();
        $todayYmd = (int) date('Ymd');

        $summaryRow = $db->table('find_item')
            ->select('COUNT(*) AS total_emprestados', false)
            ->select('SUM(CASE WHEN i_dt_prev > 0 AND i_dt_prev < ' . $todayYmd . ' THEN 1 ELSE 0 END) AS total_atrasados', false)
            ->where('i_library', $libraryCode)
            ->where('i_status', 6)
            ->get()
            ->getRowArray();

        $totalEmprestados = (int) ($summaryRow['total_emprestados'] ?? 0);
        $totalAtrasados = (int) ($summaryRow['total_atrasados'] ?? 0);
        $totalEmDia = max(0, $totalEmprestados - $totalAtrasados);

        $historyMeta = $this->resolveHistoryMeta($db);
        $loanPeriods = [
            '7' => 0,
            '14' => 0,
            '28' => 0,
        ];
        $topBorrowers = [];

        if (!empty($historyMeta['table']) && !empty($historyMeta['code_col']) && !empty($historyMeta['date_col'])) {
            $table = (string) $historyMeta['table'];
            $codeCol = (string) $historyMeta['code_col'];
            $dateCol = (string) $historyMeta['date_col'];
            $libraryCol = (string) ($historyMeta['library_col'] ?? '');
            $userCol = (string) ($historyMeta['user_col'] ?? '');

            foreach ([7, 14, 28] as $days) {
                $from = date('Y-m-d 00:00:00', strtotime('-' . $days . ' days'));

                $builder = $db->table($table . ' h')
                    ->select('COUNT(*) AS total', false)
                    ->where('h.' . $codeCol, 701)
                    ->where('h.' . $dateCol . ' >=', $from);

                if ($libraryCol !== '') {
                    $builder->where('h.' . $libraryCol, $historyLibraryId);
                }

                $row = $builder->get()->getRowArray();
                $loanPeriods[(string) $days] = (int) ($row['total'] ?? 0);
            }

            if ($userCol !== '') {
                $builderTop = $db->table($table . ' h')
                    ->select('h.' . $userCol . ' AS id_us, u.us_nome, COUNT(*) AS total', false)
                    ->join('users u', 'u.id_us = h.' . $userCol, 'left')
                    ->where('h.' . $codeCol, 701)
                    ->groupBy('h.' . $userCol)
                    ->groupBy('u.us_nome')
                    ->orderBy('total', 'DESC')
                    ->limit(5);

                if ($libraryCol !== '') {
                    $builderTop->where('h.' . $libraryCol, $historyLibraryId);
                }

                $topRows = $builderTop->get()->getResultArray();
                foreach ($topRows as $row) {
                    $topBorrowers[] = [
                        'id_us' => (int) ($row['id_us'] ?? 0),
                        'us_nome' => trim((string) ($row['us_nome'] ?? '')) !== '' ? (string) $row['us_nome'] : 'Usuário #' . (int) ($row['id_us'] ?? 0),
                        'total' => (int) ($row['total'] ?? 0),
                    ];
                }
            }
        }

        return view('Emprestimo/relatorio', [
            'library' => $library,
            'totalEmprestados' => $totalEmprestados,
            'totalAtrasados' => $totalAtrasados,
            'totalEmDia' => $totalEmDia,
            'loanPeriods' => $loanPeriods,
            'topBorrowers' => $topBorrowers,
        ]);
    }

    private function resolveHistoryMeta($db): array
    {
        $table = '';
        if ($db->tableExists('find_item_historic')) {
            $table = 'find_item_historic';
        } elseif ($db->tableExists('itens_historico')) {
            $table = 'itens_historico';
        }

        if ($table === '') {
            return [
                'table' => '',
                'code_col' => '',
                'date_col' => '',
                'library_col' => '',
                'user_col' => '',
            ];
        }

        $fields = $db->getFieldNames($table);
        $fields = is_array($fields) ? $fields : [];

        return [
            'table' => $table,
            'code_col' => $this->firstExistingColumn($fields, ['h_code', 'ih_code', 'fih_code', 'code', 'status', 'action_code']),
            'date_col' => $this->firstExistingColumn($fields, ['h_datetime', 'ih_datetime', 'fih_datetime', 'created_at', 'date', 'datetime']),
            'library_col' => $this->firstExistingColumn($fields, ['h_library', 'ih_library', 'fih_library', 'library_id', 'id_library', 'library']),
            'user_col' => $this->firstExistingColumn($fields, ['h_user', 'ih_user', 'fih_user', 'user_id', 'id_user']),
        ];
    }

    private function firstExistingColumn(array $fields, array $candidates): string
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $fields, true)) {
                return $candidate;
            }
        }

        return '';
    }


    public function loan()
    {
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        $userId = (int) ($this->request->getGet('id_us') ?? $this->request->getPost('id_us') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/emprestimo')->with('msg', 'Selecione um usuário para iniciar o empréstimo.')->with('msg_type', 'warning');
        }

        $libraryCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        $libraryModel = new \App\Models\Find\Library\Index();
        $library = $libraryModel->getSelectedLibrary($libraryCode);
        $libraryId = (int) ($library['id'] ?? 0);

        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('id_us, us_nome, us_email')
            ->where('id_us', $userId)
            ->get()
            ->getRowArray();

        if (!$user) {
            return redirect()->to('/emprestimo')->with('msg', 'Usuário não encontrado.')->with('msg_type', 'warning');
        }

        $linked = false;
        if ($libraryId > 0) {
            $linkRow = $db->table('users_library')
                ->select('id_ul')
                ->where('ul_user', $userId)
                ->where('ul_library', $libraryId)
                ->get()
                ->getRowArray();
            $linked = !empty($linkRow);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = (string) ($this->request->getPost('action') ?? '');
            $cart = $this->getLoanCart($userId, $libraryCode);

            if ($action === 'add_tombo') {
                if (!$linked) {
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Usuário não vinculado à biblioteca.')->with('msg_type', 'warning');
                }

                $tombo = (int) ($this->request->getPost('tombo') ?? 0);
                if ($tombo <= 0) {
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Informe um número tombo válido.')->with('msg_type', 'warning');
                }

                foreach ($cart as $item) {
                    if ((int) ($item['i_tombo'] ?? 0) === $tombo) {
                        return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Tombo já está na lista de empréstimo.')->with('msg_type', 'info');
                    }
                }

                $item = $this->getItemByTombo($libraryCode, $tombo);
                if (!$item) {
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Tombo não encontrado para esta biblioteca.')->with('msg_type', 'warning');
                }

                // Se já está emprestado para o mesmo usuário, marcar para devolução.
                if ((int) ($item['i_status'] ?? 0) === 6 && (int) ($item['i_usuario'] ?? 0) === $userId) {
                    $item['loan_action'] = 'return';
                    $cart[] = $item;
                    $this->setLoanCart($userId, $libraryCode, $cart);

                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Tombo marcado para devolução.')->with('msg_type', 'info');
                }

                // Bloquear empréstimo somente quando status for superior a 5.
                if ((int) ($item['i_status'] ?? 0) > 5) {
                    $status = (int) ($item['i_status'] ?? 0);
                    $msg = 'Livro não está disponível para empréstimo no momento. ';
                    $msg .= 'Status atual: ' . $this->itemStatusLabel($status) . ' (' . $status . ').';

                    if ($status === 6) {
                        $holderId = (int) ($item['i_usuario'] ?? 0);
                        $holderName = 'Usuário não identificado';
                        if ($holderId > 0) {
                            $holder = $db->table('users')
                                ->select('us_nome')
                                ->where('id_us', $holderId)
                                ->get()
                                ->getRowArray();
                            $holderName = trim((string) ($holder['us_nome'] ?? ''));
                            if ($holderName === '') {
                                $holderName = 'Usuário #' . $holderId;
                            }
                        }

                        $due = (int) ($item['i_dt_prev'] ?? 0);
                        $dueText = 'não informada';
                        if ($due > 0) {
                            $dueStr = str_pad((string) $due, 8, '0', STR_PAD_LEFT);
                            $dueText = substr($dueStr, 6, 2) . '/' . substr($dueStr, 4, 2) . '/' . substr($dueStr, 0, 4);
                        }

                        $msg .= ' Emprestado para: ' . $holderName . '. Previsão de devolução: ' . $dueText . '.';
                    }

                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', $msg)->with('msg_type', 'warning');
                }

                $item['loan_action'] = 'loan';
                $cart[] = $item;
                $this->setLoanCart($userId, $libraryCode, $cart);

                return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Tombo adicionado à lista de empréstimo.')->with('msg_type', 'success');
            }

            if ($action === 'remove_tombo') {
                $tombo = (int) ($this->request->getPost('tombo') ?? 0);
                $cart = array_values(array_filter($cart, static function ($it) use ($tombo) {
                    return (int) ($it['i_tombo'] ?? 0) !== $tombo;
                }));
                $this->setLoanCart($userId, $libraryCode, $cart);
                return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Tombo removido da lista.')->with('msg_type', 'info');
            }

            if ($action === 'cancel_loan') {
                $this->clearLoanCart($userId, $libraryCode);
                return redirect()->to('/emprestimo')->with('msg', 'Empréstimo cancelado.')->with('msg_type', 'info');
            }

            if ($action === 'finalize_loan') {
                if (!$linked) {
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Usuário não vinculado à biblioteca.')->with('msg_type', 'warning');
                }

                if (empty($cart)) {
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Nenhum item na lista para emprestar.')->with('msg_type', 'warning');
                }

                $hasLoanItems = false;
                foreach ($cart as $it) {
                    if ((string) ($it['loan_action'] ?? 'loan') === 'loan') {
                        $hasLoanItems = true;
                        break;
                    }
                }

                $todayDate = date('Y-m-d');
                $dueDate = trim((string) ($this->request->getPost('due_date') ?? ''));
                if ($hasLoanItems) {
                    if ($dueDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
                        $dueDate = date('Y-m-d', strtotime('+7 days'));
                    }
                    if ($dueDate < $todayDate) {
                        return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'A data de devolução não pode ser anterior a hoje.')->with('msg_type', 'warning');
                    }
                }

                $today = (int) date('Ymd');
                $due = $hasLoanItems ? (int) date('Ymd', strtotime($dueDate)) : 0;
                $sendEmail = (int) ($this->request->getPost('send_email') ?? 0) === 1;
                $historyLibraryId = is_numeric($libraryCode) ? (int) $libraryCode : $libraryId;
                $historyModel = new ItemHistoric();

                $loanCount = 0;
                $returnCount = 0;

                $db->transBegin();

                foreach ($cart as $it) {
                    $itemId = (int) ($it['id_i'] ?? 0);
                    $tombo = (int) ($it['i_tombo'] ?? 0);
                    $itemAction = (string) ($it['loan_action'] ?? 'loan');
                    if ($itemId <= 0 || $tombo <= 0) {
                        continue;
                    }

                    if ($itemAction === 'return') {
                        $db->table('find_item')
                            ->where('id_i', $itemId)
                            ->where('i_tombo', $tombo)
                            ->where('i_library', $libraryCode)
                            ->where('i_usuario', $userId)
                            ->where('i_status', 6)
                            ->update([
                                'i_status' => 1,
                                'i_usuario' => 0,
                                'i_dt_emprestimo' => 0,
                                'i_dt_prev' => 0,
                                'i_dt_renovavao' => 0,
                            ]);
                        $returnCount++;

                        $historyModel->registerMovement(702, $userId, $tombo, $historyLibraryId, $itemId);

                        continue;
                    }

                    $db->table('find_item')
                        ->where('id_i', $itemId)
                        ->where('i_tombo', $tombo)
                        ->where('i_library', $libraryCode)
                        ->update([
                            'i_status' => 6,
                            'i_usuario' => $userId,
                            'i_dt_emprestimo' => $today,
                            'i_dt_prev' => $due,
                            'i_dt_renovavao' => 0,
                        ]);
                    $loanCount++;

                    $historyModel->registerMovement(701, $userId, $tombo, $historyLibraryId, $itemId);
                }

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    return redirect()->to('/emprestimo/loan?id_us=' . $userId)->with('msg', 'Erro ao finalizar empréstimo.')->with('msg_type', 'danger');
                }

                $db->transCommit();
                $this->clearLoanCart($userId, $libraryCode);

                $msg = 'Finalização concluída: ' . $loanCount . ' empréstimo(s) e ' . $returnCount . ' devolução(ões).';
                if ($sendEmail) {
                    $msg .= ' Envio de e-mail solicitado.';
                }

                return redirect()->to('/emprestimo')->with('msg', $msg)->with('msg_type', 'success');
            }
        }

        $cart = $this->getLoanCart($userId, $libraryCode);

        $todayYmd = (int) date('Ymd');
        $activeLoansRows = $db->table('find_item')
            ->select('id_i, i_tombo, i_titulo, i_dt_emprestimo, i_dt_prev, i_status')
            ->where('i_usuario', $userId)
            ->where('i_library', $libraryCode)
            ->where('i_status', 6)
            ->orderBy('i_dt_prev', 'ASC')
            ->get()
            ->getResultArray();

        $activeLoans = [];
        $loanTotal = 0;
        $loanOverdue = 0;

        foreach ($activeLoansRows as $row) {
            $loanTotal++;
            $due = (int) ($row['i_dt_prev'] ?? 0);
            $isOverdue = $due > 0 && $due < $todayYmd;
            if ($isOverdue) {
                $loanOverdue++;
            }

            $activeLoans[] = [
                'i_tombo' => $row['i_tombo'] ?? '',
                'i_titulo' => $row['i_titulo'] ?? '',
                'i_dt_prev' => $due,
                'is_overdue' => $isOverdue,
            ];
        }

        $loanSummary = [
            'total' => $loanTotal,
            'overdue' => $loanOverdue,
            'on_time' => max(0, $loanTotal - $loanOverdue),
        ];

        return view('Emprestimo/loan', [
            'user' => $user,
            'library' => $library,
            'linked' => $linked,
            'cart' => $cart,
            'activeLoans' => $activeLoans,
            'loanSummary' => $loanSummary,
        ]);
    }

    public function returnLoan()
    {
        if ($resp = $this->denyIfNoPermission()) {
            return $resp;
        }

        return $this->response->setJSON([
            'status' => '200',
            'message' => 'Endpoint de devolucao ativo.',
            'data' => $this->request->getPost() ?: $this->request->getGet(),
        ]);
    }
}
