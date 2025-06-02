<?php

namespace App\Models\Social;

use CodeIgniter\Model;

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

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $RSP = [];
        $RSP['post'] = $_POST;
        $verb = get("verb");
        $RSP['verb'] = $verb;
        $RSP['username'] = get("username");

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

    function apiKey($dt,$new = false)
        {
            if (($dt['us_apikey'] == '') or ($new == true))
                {
                    $apikey = md5($dt['us_nome'].date("Ymd-His"));
                    $dd = [];
                    $dd['us_apikey'] = $apikey;
                    $this->set($dd)->where('id_us',$dt['id_us'])->update();
                    $dt['us_apikey'] = $apikey;
                }
            return $dt['us_apikey'];
        }

    /********************************** FORGOT */
    function signin($login)
    {
        $RSP = [];
        $pass = get("password");

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

        $dt = $this->where('us_email', $login)->first();

        if ($dt != []) {
            if (md5($pass) == $dt['us_password'])
                {
                    $nickname = $dt['us_nickname'];
                    $fullname = $dt['us_nome'];
                    if ($nickname == '')
                        {
                            $nickname = substr($fullname,0,strpos($fullname,' '));
                        }
                    $RSP['status'] = '200';
                    $RSP['message'] = 'Usuário OK';
                    $RSP['html'] = 'Usuário OK';
                    $RSP['apikey'] = $this->apiKey($dt);
                    $RSP['fullname'] = $dt['us_nome'];
                    $RSP['nickname'] = $nickname;
                    $RSP['email'] = $dt['us_email'];
                    $RSP['ID'] = $dt['id_us'];
                    $RSP['perfil'] = ['admin'=>false];
                } else {
                    $RSP['status'] = '400';
                    $RSP['message'] = 'Usuário ou senha inválida';
                    $RSP['html'] = 'Usuário ou senha inválida';
                }
        } else {
            $RSP['status'] = '400';
            $RSP['message'] = 'Usuário ou senha inválida';
            $RSP['html'] = 'Usuário ou senha inválida';
        }
        return $RSP;
    }

    /********************************** FORGOT */
    function forgot($login)
    {
        $RSP = [];
        $dt = $this->where('us_email', $login)->first();

        if ($dt != []) {
            /* Send e-mail with information */
            $from = 'rene.gabriel@ufrgs.br';
            $subject = 'Cadastro de nova senha';
            $text = 'Link de senha';
            $text .= PATH . '#/social/password';
            $img = [];
            $RSP['html'] = 'Link enviado por e-mail: ';
            $RSP['html'] .= $dt['us_email'];
            if ($this->sendemail($dt['us_email'], $from, $subject, $text, $img)) {
                $RSP['html'] .= '<p>Um e-mail foi enviado para sua conta</p>';
            }
        } else {
            $RSP['status'] = '201';
            $RSP['message'] = 'E-mail não cadastrado';
            $RSP['html'] = 'E-mail não cadastrado';
            return $RSP;
        }
        return $RSP;
    }
    function sendemail($to, $from, $subject, $text, $img)
    {
        $email = \Config\Services::email();

        $email->setFrom($from, 'FIND');
        $email->setTo($to);

        $email->setSubject($subject);
        $email->setMessage($text);

        if ($email->send()) {
            return true;
        } else {
            $this->data = $email->printDebugger(['headers']);
            return false;
        }
    }
}
