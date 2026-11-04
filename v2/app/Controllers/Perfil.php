<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Perfil extends Controller
{
    public function password()
    {
        $userId = session()->get('id_us');
        if (!$userId) {
            return redirect()->to('/login');
        }

        helper(['form', 'url']);
        if ($this->request->getMethod() !== 'post') {
            return view('Users/change_password');
        }

        $current = $this->request->getPost('current_password');
        $password = $this->request->getPost('new_password');
        $confirmation = $this->request->getPost('confirm_password');
        $error = null;

        if (!is_string($current) || !is_string($password) || !is_string($confirmation)
            || $current === '' || $password === '' || $confirmation === '') {
            $error = 'Preencha todos os campos.';
        } elseif (strlen($password) < 8 || strlen($password) > 72) {
            $error = 'A nova senha deve ter entre 8 e 72 bytes.';
        } elseif ($password !== trim($password)) {
            $error = 'A nova senha não pode começar ou terminar com espaços.';
        } elseif ($password !== $confirmation) {
            $error = 'A confirmação não corresponde à nova senha.';
        } elseif (strpos($password, "\0") !== false) {
            $error = 'A nova senha contém um caractere inválido.';
        }

        if ($error !== null) {
            return view('Users/change_password', ['error' => $error]);
        }

        $model = new \App\Models\Social\Social();
        $user = $model->find($userId);
        $stored = (string) ($user['us_password'] ?? '');
        $current = trim($current);
        if ($stored === '' || !(password_verify($current, $stored) || hash_equals($stored, md5($current)))) {
            return view('Users/change_password', ['error' => 'A senha atual está incorreta.']);
        }

        $apiKey = bin2hex(random_bytes(16));
        if (!$model->update($userId, [
            'us_password' => password_hash($password, PASSWORD_DEFAULT),
            'us_apikey' => $apiKey,
        ])) {
            return view('Users/change_password', ['error' => 'Não foi possível salvar a senha. Tente novamente.']);
        }

        session()->regenerate(true);
        session()->set('apikey', $apiKey);
        return redirect()->to('/perfil')->with('password_success', 'Senha alterada com sucesso.');
    }

    public function index()
    {
        $userId = session()->get('id_us');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users_group_members m')
            ->select('m.*, g.gr_name, l.l_name')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'left')
            ->join('library l', 'l.l_code = m.grm_library', 'left')
            ->where('m.grm_user', $userId)
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)')
            ->orderBy('l.l_name, g.gr_name');
        $perfis = $builder->get()->getResultArray();

        return view('perfil', [
            'perfis' => $perfis
        ]);
    }
}
