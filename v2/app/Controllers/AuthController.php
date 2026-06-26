<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Social\Social;

class AuthController extends BaseController
{
    public function index()
    {
        echo "OK";
        exit;
        return redirect()->to('/login');
    }

    public function login()
    {
        helper(['form', 'url']);
        return view('Users/login');
    }

    public function forgotPassword()
    {
        helper(['form', 'url']);
        return view('Users/forgot_password');
    }

    public function sendPasswordReset()
    {
        $model = new Social();
        $email = trim((string) $this->request->getVar('us_email'));

        if ($email === '') {
            return redirect()->back()->with('msg', 'Informe seu e-mail.')->with('msg_type', 'warning');
        }

        $result = $model->forgot($email);

        if (($result['status'] ?? '500') === '200') {
            return redirect()->to('/forgot-password')->with('msg', 'Se o e-mail estiver cadastrado, a mensagem de recuperação foi enviada.')->with('msg_type', 'success');
        }

        return redirect()->back()->with('msg', $result['message'] ?? 'Não foi possível processar o pedido.')->with('msg_type', 'warning');
    }

    public function resetPassword($token)
    {
        $model = new Social();
        $user = $model->where('us_apikey', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Link inválido ou expirado.')->with('msg_type', 'danger');
        }

        return view('Users/reset_password', ['token' => $token]);
    }

    public function updatePassword()
    {
        $model = new Social();
        $token = (string) $this->request->getVar('token');
        $newPassword = (string) $this->request->getVar('us_password');
        $confirmPassword = (string) $this->request->getVar('confirm_password');

        if ($newPassword === '' || $newPassword !== $confirmPassword) {
            return redirect()->back()->with('msg', 'As senhas não coincidem.')->with('msg_type', 'warning');
        }

        $user = $model->where('us_apikey', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Link inválido ou expirado.')->with('msg_type', 'danger');
        }

        $model->update($user['id_us'], [
            'us_password' => md5($newPassword),
            'us_apikey' => md5(($user['us_nome'] ?? 'find') . date('Ymd-His')),
        ]);

        return redirect()->to('/login')->with('msg', 'Senha redefinida com sucesso!')->with('msg_type', 'success');
    }

    public function register()
    {
        helper(['form', 'url']);
        return view('Users/register');
    }

    public function storeUser()
    {
        $model = new Social();

        $result = $model->registerUser([
            'us_nome' => $this->request->getVar('us_nome'),
            'us_email' => $this->request->getVar('us_email'),
            'us_login' => $this->request->getVar('us_login'),
            'us_password' => $this->request->getVar('us_password'),
        ]);

        if (($result['status'] ?? '500') !== '200') {
            return redirect()->back()->withInput()->with('msg', $result['message'] ?? 'Não foi possível cadastrar.')->with('msg_type', 'warning');
        }

        return redirect()->to('/login')->with('msg', 'Cadastro realizado com sucesso.')->with('msg_type', 'success');
    }

    public function authenticate()
    {
        $session = session();
        $model = new Social();

        $login = trim((string) $this->request->getVar('us_login'));
        $password = (string) $this->request->getVar('us_password');

        $user = $model->authenticateUser($login, $password);

        if (!$user) {
            return redirect()->to('/login')->withInput()->with('msg', 'Usuário ou senha inválida.')->with('msg_type', 'danger');
        }

        $session->set([
            'id_us' => $user['id_us'] ?? null,
            'us_nome' => $user['us_nome'] ?? '',
            'first_name' => $model->firstName($user['us_nome'] ?? ''),
            'us_email' => $user['us_email'] ?? '',
            'apikey' => $user['us_apikey'] ?? '',
            'logged_in' => true,
        ]);

        return redirect()->to('/')->with('msg', 'Login realizado com sucesso.')->with('msg_type', 'success');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('msg', 'Sessão encerrada.')->with('msg_type', 'info');
    }
}
