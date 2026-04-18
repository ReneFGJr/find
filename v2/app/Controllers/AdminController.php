<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function configuration()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('msg', 'Faça login para acessar.')->with('msg_type', 'warning');
        }

        $cookieCode = trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
        if ($cookieCode === '') {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $libModel = new \App\Models\Find\Library\Index();
        $library = $libModel->getSelectedLibrary($cookieCode);

        return view('Admin/configuration', [
            'library' => $library,
        ]);
    }

    private function getLibraryCode()
    {
        helper(['url', 'cookie']);
        if (!session()->get('logged_in')) {
            return null;
        }
        return trim((string) (get_cookie('library_code') ?? get_cookie('library') ?? ''));
    }

    public function roles()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('msg', 'Faça login para acessar.')->with('msg_type', 'warning');
        }

        $cookieCode = $this->getLibraryCode();
        if ($cookieCode === '' || $cookieCode === null) {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $groupModel = new \App\Models\User\UserGroup();
        $groups = $groupModel->groups($cookieCode);

        $libModel = new \App\Models\Find\Library\Index();
        $library = $libModel->getSelectedLibrary($cookieCode);

        return view('Admin/roles', [
            'groups' => $groups,
            'library' => $library,
            'libraryCode' => $cookieCode,
        ]);
    }

    public function addMember()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => '401', 'message' => 'Não autorizado']);
        }

        $cookieCode = $this->getLibraryCode();
        $userId = $this->request->getPost('id_us');
        $groupId = $this->request->getPost('id_gr');

        if (!$userId || !$groupId || !$cookieCode) {
            return $this->response->setJSON(['status' => '400', 'message' => 'Dados incompletos']);
        }

        $memberModel = new \App\Models\User\UserGroupMember();
        $result = $memberModel->addToGroup($userId, $groupId, $cookieCode);

        return $this->response->setJSON($result);
    }

    public function disableMember()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => '401', 'message' => 'Não autorizado']);
        }

        $id_grm = $this->request->getPost('id_grm');
        if (!$id_grm) {
            return $this->response->setJSON(['status' => '400', 'message' => 'ID não informado']);
        }

        $memberModel = new \App\Models\User\UserGroupMember();
        $result = $memberModel->disableMember($id_grm);

        return $this->response->setJSON($result);
    }

    public function searchUsers()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON([]);
        }

        $term = trim((string) $this->request->getGet('q'));
        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $userModel = new \App\Models\User\User();
        $users = $userModel
            ->select('id_us, us_nome, us_email')
            ->like('us_nome', $term)
            ->orLike('us_email', $term)
            ->orderBy('us_nome')
            ->findAll(20);

        return $this->response->setJSON($users);
    }

    public function places()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('msg', 'Faça login para acessar.')->with('msg_type', 'warning');
        }

        $cookieCode = $this->getLibraryCode();
        if ($cookieCode === '' || $cookieCode === null) {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $placeModel = new \App\Models\Find\Library\LibraryPlace();
        $places = $placeModel->listByLibrary($cookieCode);

        $libModel = new \App\Models\Find\Library\Index();
        $library = $libModel->getSelectedLibrary($cookieCode);

        return view('Admin/places', [
            'places' => $places,
            'library' => $library,
            'libraryCode' => $cookieCode,
        ]);
    }

    public function createPlace()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => '401', 'message' => 'Não autorizado']);
        }

        $cookieCode = $this->getLibraryCode();
        $name = trim((string) $this->request->getPost('lp_name'));

        if ($name === '' || !$cookieCode) {
            return $this->response->setJSON(['status' => '400', 'message' => 'Nome é obrigatório']);
        }

        $placeModel = new \App\Models\Find\Library\LibraryPlace();
        $result = $placeModel->createPlace([
            'lp_name' => $name,
            'lp_LIBRARY' => $cookieCode,
            'lp_active' => 1,
        ]);

        return $this->response->setJSON($result);
    }

    public function updatePlaceName()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => '401', 'message' => 'Não autorizado']);
        }

        $id = $this->request->getPost('id_lp');
        $name = trim((string) $this->request->getPost('lp_name'));

        if (!$id || $name === '') {
            return $this->response->setJSON(['status' => '400', 'message' => 'Dados incompletos']);
        }

        $placeModel = new \App\Models\Find\Library\LibraryPlace();
        $result = $placeModel->updateName($id, $name);

        return $this->response->setJSON($result);
    }

    public function library()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('msg', 'Faça login para acessar.')->with('msg_type', 'warning');
        }

        $cookieCode = $this->getLibraryCode();
        if ($cookieCode === '' || $cookieCode === null) {
            return redirect()->to('/bibliotecas')->with('msg', 'Selecione uma biblioteca.')->with('msg_type', 'warning');
        }

        $libModel = new \App\Models\Find\Library\Index();
        $library = $libModel->getSelectedLibrary($cookieCode);

        if (!$library) {
            return redirect()->to('/admin/configuration')->with('msg', 'Biblioteca não encontrada.')->with('msg_type', 'danger');
        }

        // Busca o registro bruto para edição
        $raw = $libModel
            ->groupStart()
                ->where('l_code', $cookieCode)
                ->orWhere('id_l', $cookieCode)
            ->groupEnd()
            ->first();

        return view('Admin/library', [
            'library' => $library,
            'raw' => $raw,
        ]);
    }

    public function saveLibrary()
    {
        helper(['url', 'cookie']);

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => '401', 'message' => 'Não autorizado']);
        }

        $cookieCode = $this->getLibraryCode();
        if (!$cookieCode) {
            return $this->response->setJSON(['status' => '400', 'message' => 'Biblioteca não selecionada']);
        }

        $libModel = new \App\Models\Find\Library\Index();
        $raw = $libModel
            ->groupStart()
                ->where('l_code', $cookieCode)
                ->orWhere('id_l', $cookieCode)
            ->groupEnd()
            ->first();

        if (!$raw) {
            return $this->response->setJSON(['status' => '404', 'message' => 'Biblioteca não encontrada']);
        }

        $id = $raw['id_l'];
        $data = [
            'l_name'    => trim((string) $this->request->getPost('l_name')),
            'l_about'   => trim((string) $this->request->getPost('l_about')),
            'l_visible' => $this->request->getPost('l_visible') ? 1 : 0,
            'l_net'     => trim((string) $this->request->getPost('l_net')),
        ];

        if ($data['l_name'] === '') {
            return $this->response->setJSON(['status' => '400', 'message' => 'O nome da biblioteca é obrigatório.']);
        }

        $libModel->update($id, $data);

        return $this->response->setJSON(['status' => '200', 'message' => 'Dados atualizados com sucesso.']);
    }
}
