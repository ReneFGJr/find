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
        'us_institution', 'us_perfil', 'us_login'
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

    function index($d1='',$d2='',$d3='')
        {
            $RSP = [];
            $RSP['post'] = $_POST;
            $verb = get("verb");
            $RSP['verb'] = $verb;
            $RSP['username'] = get("username");

            switch($verb)
                {
                    case 'forgot':
                        $this->forgot($RSP['username']);
                        break;
                }
            return $RSP;
        }
        function forgot($login)
            {
                $RSP = '';
                $dt = $this->where('us_email',$login)->first();

                if ($dt != [])
                    {
                        /* Send e-mail with information */
                        $from = 'rene@sisdoc.com.br';
                        $subject = 'Cadastro de nova senha';
                        $text = 'Link de senha';
                        $img = [];

                        $this->sendemail($dt['us_email'],$from,$subject,$text,$img);
                    } else {
                        $RSP['status'] = '201';
                        $RSP['message'] = 'E-mail não cadastrado';
                        return $RSP;
                    }
                pre($dt);
            }
        function sendemail($to,$from,$subject,$text,$img)
            {
                $email = \Config\Services::email();

                $email->setFrom('seuemail@exemplo.com', 'Seu Nome');
                $email->setTo('destinatario@exemplo.com');

                $email->setSubject('Assunto do E-mail');
                $email->setMessage('Esta é a mensagem do e-mail.');

                if ($email->send()) {
                    echo 'Email enviado com sucesso!';
                } else {
                    $data = $email->printDebugger(['headers']);
                    pre($data);
                }
            }
}
