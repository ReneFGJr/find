<?php

namespace App\Models\Social;

use CodeIgniter\Model;

helper('sisdoc');

class Social extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id_us';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_us', 'us_nome', 'us_email',
        'us_cidade', 'us_pais', 'us_codigo',
        'us_badge', 'us_link', 'us_ativo',
        'us_nivel', 'us_image', 'us_genero',
        'us_verificado', 'us_autenticador', 'us_password',
        'us_institution', 'us_perfil', 'us_login', 'us_apikey'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public $data = [];

    public function firstName(string $name = ''): string
    {
        $name = trim($name);
        if ($name === '') {
            return 'Usuário';
        }

        $parts = preg_split('/\s+/', $name);
        return $parts[0] ?? $name;
    }

    public function authenticateUser(string $login, string $password): ?array
    {
        $login = trim($login);
        $password = trim($password);

        if ($login === '' || $password === '') {
            return null;
        }

        $user = $this->groupStart()
            ->where('us_email', $login)
            ->orWhere('us_login', $login)
            ->groupEnd()
            ->first();

        if (!is_array($user)) {
            return null;
        }

        $storedPassword = (string) ($user['us_password'] ?? '');
        $valid = false;

        if ($storedPassword !== '') {
            $valid = hash_equals($storedPassword, md5($password));
            $hashInfo = password_get_info($storedPassword);

            if (!$valid && !empty($hashInfo['algo'])) {
                $valid = password_verify($password, $storedPassword);
            }
        }

        if (!$valid) {
            return null;
        }

        $user['us_apikey'] = $this->apiKey($user);
        return $user;
    }

    public function registerUser(array $data): array
    {
        $name = trim((string) ($data['us_nome'] ?? ''));
        $email = trim((string) ($data['us_email'] ?? ''));
        $login = trim((string) ($data['us_login'] ?? $email));
        $password = (string) ($data['us_password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            return ['status' => '400', 'message' => 'Preencha nome, e-mail e senha.'];
        }

        $exists = $this->groupStart()
            ->where('us_email', $email)
            ->orWhere('us_login', $login)
            ->groupEnd()
            ->first();

        if (is_array($exists)) {
            return ['status' => '409', 'message' => 'Já existe um usuário com este login ou e-mail.'];
        }

        $id = $this->insert([
            'us_nome' => $name,
            'us_email' => $email,
            'us_login' => $login === '' ? $email : $login,
            'us_password' => md5($password),
            'us_ativo' => 1,
        ], true);

        if (!$id) {
            return ['status' => '500', 'message' => 'Não foi possível cadastrar o usuário.'];
        }

        return ['status' => '200', 'message' => 'Cadastro realizado com sucesso.', 'id' => $id];
    }

    public function index($d1 = '', $d2 = '', $d3 = '')
    {
        $RSP = [];
        $RSP['post'] = $_POST;
        $verb = get('verb');
        $RSP['verb'] = $verb;
        $RSP['username'] = get('username');

        switch ($verb) {
            case 'signin':
                $RSP['user'] = $this->signin($RSP['username']);
                break;

            case 'forgot':
                $RSP['user'] = $this->forgot($RSP['username']);
                break;
        }

        return $RSP;
    }

    public function apiKey($dt, $new = false)
    {
        if ((empty($dt['us_apikey'])) || ($new === true)) {
            $apikey = md5(($dt['us_nome'] ?? 'find') . date('Ymd-His'));
            $this->set(['us_apikey' => $apikey])->where('id_us', $dt['id_us'])->update();
            $dt['us_apikey'] = $apikey;
        }

        return $dt['us_apikey'];
    }

    public function signin($login)
    {
        $RSP = [];
        $pass = get('password');

        if ($pass == '') {
            $RSP['status'] = '400';
            $RSP['message'] = 'Senha não informada';
            $RSP['html'] = 'Senha não informada';
            return $RSP;
        }

        if ($login == '') {
            $RSP['status'] = '400';
            $RSP['message'] = 'Login não informado';
            $RSP['html'] = 'Login não informado';
            return $RSP;
        }

        $dt = $this->authenticateUser($login, $pass);

        if (is_array($dt)) {
            $nickname = (string) ($dt['us_nickname'] ?? '');
            $fullname = (string) ($dt['us_nome'] ?? '');

            if ($nickname == '') {
                $nickname = $this->firstName($fullname);
            }

            $RSP['status'] = '200';
            $RSP['message'] = 'Usuário OK';
            $RSP['html'] = 'Usuário OK';
            $RSP['apikey'] = $dt['us_apikey'];
            $RSP['fullname'] = $fullname;
            $RSP['nickname'] = $nickname;
            $RSP['email'] = $dt['us_email'] ?? '';
            $RSP['ID'] = $dt['id_us'] ?? 0;
            $RSP['perfil'] = ['admin' => false];
        } else {
            $RSP['status'] = '400';
            $RSP['message'] = 'Usuário ou senha inválida';
            $RSP['html'] = 'Usuário ou senha inválida';
        }

        return $RSP;
    }

    public function forgot($login)
    {
        helper('url');

        $RSP = [];
        $dt = $this->where('us_email', $login)->first();

        if (is_array($dt)) {
            $token = $this->apiKey($dt, true);
            $from = 'no-reply@find.local';
            $subject = 'Cadastro de nova senha';
            $link = base_url('/reset-password/' . $token);
            $text = '<p>Olá, ' . esc($dt['us_nome'] ?? 'usuário') . '.</p>';
            $text .= '<p>Recebemos um pedido para redefinir sua senha no FIND.</p>';
            $text .= '<p><a href="' . $link . '">Clique aqui para criar uma nova senha</a></p>';
            $img = [];
            $RSP['status'] = '200';
            $RSP['message'] = 'Link enviado por e-mail';
            $RSP['html'] = 'Link enviado por e-mail: ' . ($dt['us_email'] ?? '');

            if ($this->sendemail($dt['us_email'], $from, $subject, $text, $img)) {
                $RSP['html'] .= '<p>Um e-mail foi enviado para sua conta</p>';
            } else {
                $RSP['status'] = '500';
                $RSP['message'] = 'Não foi possível enviar o e-mail';
            }
        } else {
            $RSP['status'] = '201';
            $RSP['message'] = 'E-mail não cadastrado';
            $RSP['html'] = 'E-mail não cadastrado';
        }

        return $RSP;
    }

    public function sendemail($to, $from, $subject, $text, $img)
    {
        $email = \Config\Services::email();

        $email->setFrom($from, 'FIND');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($text);

        if ($email->send()) {
            return true;
        }

        $this->data = $email->printDebugger(['headers']);
        return false;
    }
}
