<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User\User;

class AuthController extends BaseController
{
    public function index()
    {
        //
    }

    public function resetPassword($token)
    {
        $model = new User();
        $user = $model->where('us_oauth2', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Token inválido ou expirado.');
        }
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('Users/reset_password', ['token' => $token]);
        return $sx;
    }

    public function updatePassword()
    {
        $model = new User();
        $token = $this->request->getVar('token');
        $newPassword = $this->request->getVar('us_password');

        $user = $model->where('us_oauth2', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Token inválido ou expirado.');
        }

        $model->update($user['id_us'], [
            'us_password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'us_oauth2' => null // Invalida o token após o uso
        ]);

        return redirect()->to('/login')->with('msg', 'Senha redefinida com sucesso!');
    }

    public function login()
    {
        helper(['form']);
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('Users/login');
        return $sx;
    }

    public function forgotPassword()
    {
        helper(['form']);
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('Users/forgot_password');
        return $sx;
    }

    public function sendPasswordReset()
    {
        $model = new User();
        $us_email = $this->request->getVar('us_email');
        $user = $model->where('us_email', $us_email)->first();

        if ($user) {
            // Gerar um hash único para redefinição
            $token = bin2hex(random_bytes(16));
            $model->update($user['id_us'], ['us_oauth2' => $token]);

            // Criar o link de redefinição
            $resetLink = base_url("/reset-password/$token");
            echo $resetLink;
            exit;

            // Enviar e-mail ao usuário
            $email = \Config\Services::email();

            $email->setFrom('no-reply@seusite.com', 'Seu Sistema');
            $email->setTo($us_email);
            $email->setSubject('Redefinição de Senha');
            $email->setMessage("
            <p>Olá, {$user['us_nome']}</p>
            <p>Recebemos uma solicitação para redefinir sua senha. Clique no link abaixo para redefini-la:</p>
            <p><a href='{$resetLink}'>{$resetLink}</a></p>
            <p>Se você não solicitou a redefinição, ignore este e-mail.</p>
        ");

            if ($email->send()) {
                return redirect()->to('/login')->with('msg', 'Um link de redefinição foi enviado para o seu e-mail.');
            } else {
                return redirect()->back()->with('msg', 'Não foi possível enviar o e-mail. Tente novamente.');
            }
        } else {
            return redirect()->back()->with('msg', 'E-mail não encontrado.');
        }
    }

    public function register()
    {
        helper(['form']);
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('Users/register');
        return $sx;
    }

    public function storeUser()
    {
        $model = new User();

        $data = [
            'us_nome' => $this->request->getVar('us_nome'),
            'us_email' => $this->request->getVar('us_email'),
            'us_login' => $this->request->getVar('us_login'),
            'us_password' => password_hash($this->request->getVar('us_password'), PASSWORD_DEFAULT),
        ];

        $model->insert($data);
        return redirect()->to('/login')->with('msg', 'Cadastro realizado com sucesso.');
    }

    public function authenticate()
    {
        $session = session();
        $model = new User();

        $us_login = $this->request->getVar('us_login');
        $us_password = $this->request->getVar('us_password');

        $user = $model->where('us_login', $us_login)->first();

        if ($user) {
            if (password_verify($us_password, $user['us_password'])) {
                $sessionData = [
                    'id_us' => $user['id_us'],
                    'us_nome' => $user['us_nome'],
                    'us_nickname' => $user['us_nickname'],
                    'logged_in' => true
                ];
                $session->set($sessionData);
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('msg', 'Senha incorreta.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Usuário não encontrado.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
