<?php

namespace App\Models\Z39.50;

use CodeIgniter\Model;

class SourceUfsc extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sourceufscs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    public function index($isbn = '9786589167501')
    {
        // Monta a URL com o ISBN dinâmico
        $url = "https://catalogo.bu.ufsc.br/zbib/?searchString={$isbn}&searchType=7&operator=and&searchString2=&searchType2=1003&targets%5B0%5D=on&targets%5B1%5D=on&targets%5B2%5D=on&targets%5B3%5D=on&targets%5B4%5D=on&targets%5B5%5D=on&targets%5B6%5D=on&targets%5B7%5D=on&targets%5B8%5D=on&targets%5B20%5D=on";

        // Faz a requisição HTTP
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header'  => "User-Agent: CI4-App\r\n"
            ]
        ]);

        $response = file_get_contents($url, false, $context);

        if (!$response) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Não foi possível acessar a URL'
            ]);
        }

        // Quebra o retorno em linhas
        $lines = explode("\n", $response);

        // Transforma em uma array estruturada
        $marcArray = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Cada linha MARC costuma vir como "XXX  Campo conteúdo"
            // Exemplo: "245 10 $aTítulo :$bSubtítulo."
            $tag = substr($line, 0, 3); // pega o código do campo
            $content = substr($line, 3); // pega o restante

            $marcArray[] = [
                'tag' => $tag,
                'content' => trim($content)
            ];
        }

        return $this->response->setJSON($marcArray);
    }
}
