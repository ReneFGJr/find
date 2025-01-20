<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

class IndiceReverso extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'indicereversos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;

    function removerPreposicoes($texto)
    {
        // Lista de preposições comuns em português
        $preposicoes = [
            'a',
            'ante',
            'após',
            'até',
            'com',
            'contra',
            'de',
            'desde',
            'em',
            'entre',
            'para',
            'per',
            'perante',
            'por',
            'sem',
            'sob',
            'sobre',
            'trás',
            'ao',
            'dos',
            'das',
            'no',
            'nos',
            'na',
            'nas',
            'dum',
            'duma',
            'duns',
            'dumas',
            'num',
            'numa',
            'nuns',
            'numas'
        ];

        // Converte a string para minúsculas e divide em palavras
        $palavras = explode(' ', strtolower($texto));

        // Remove as preposições
        $resultado = array_filter($palavras, function ($palavra) use ($preposicoes) {
            return !in_array($palavra, $preposicoes);
        });

        // Retorna a string sem preposições
        return implode(' ', $resultado);
    }

    // Função de busca no índice reverso
    public function search($texto)
    {
        $texto = $this->removerPreposicoes($texto);

        $IndiceReversoTermo = new IndiceReversoTermo();
        $IndiceReversoDocumento = new IndiceReversoDocumento();

        // Preprocessar o texto
        $conteudo = strtolower(ascii($texto));
        $tokens = array_unique(explode(' ', preg_replace('/[^a-z0-9 ]/', '', $conteudo)));

        // Criar a consulta dinâmica para os termos
        foreach ($tokens as $index => $tk) {
            if ($index === 0) {
                $IndiceReversoTermo->where('t_termo', $tk);
            } else {
                $IndiceReversoTermo->orWhere('t_termo', $tk);
            }
        }

        $termos = $IndiceReversoTermo->findAll();

        if (count($termos) > 0) {
            // Buscar documentos associados aos termos encontrados
            $IndiceReversoDocumento->select('w_TITLE,w_AUTHORS, d_doc, 0 as score');
            $IndiceReversoDocumento->join('find_work', 'id_w = d_doc');

            foreach ($termos as $index => $termo) {
                if ($index === 0) {
                    $IndiceReversoDocumento->where('d_termo', $termo['id_t']);
                } else {
                    $IndiceReversoDocumento->orWhere('d_termo', $termo['id_t']);
                }
            }
            $documentos = $IndiceReversoDocumento->findAll();


            $docs = [];
            $scor = [];

            foreach ($documentos as $id => $line) {
                $rev = 0;
                foreach ($tokens as $tk) {
                    $rev = $IndiceReversoTermo->calcula_relevancia($tk, $line['w_TITLE']) / count($tokens);
                    if (!isset($documentos[$id]['score'])) {
                        $documentos[$id]['score'] = $rev;
                    } else {
                        $documentos[$id]['score'] = $documentos[$id]['score'] + $rev;
                    }
                    $scor[$id] = $rev;
                }
            }

            $docs = [];
            foreach ($documentos as $id => $line) {
                $doc = $id;
                $score = $line['score'] * 2;

                if (!isset($docs[$doc])) {
                    $docs[$doc] = $score;
                } else {
                    $docs[$doc] = $docs[$doc] + $score;
                }
            }
            arsort($docs);

            $rst = [];
            // Retornar os documentos encontrados
            $xdoc = [];
            foreach ($docs as $doc => $score) {
                $documentos[$doc]['score'] = $score;
                $ndoc = $documentos[$doc]['d_doc'];
                if (!isset($xdoc[$ndoc])) {
                    array_push($rst, $documentos[$doc]);
                }
                $xdoc[$ndoc] = 1;
            }
        } else {
            $rst = [];
        }
        return $rst;
    }

    // Limpar os índices reversos
    public function truncate()
    {
        $this->db->query('TRUNCATE indice_reverso_docs');
        $this->db->query('TRUNCATE indice_reverso_termos');
    }

    // Função de indexação
    public function indexar($texto = '', $documentoId = 0)
    {
        $IndiceReversoTermo = new IndiceReversoTermo();
        $IndiceReversoDocumento = new IndiceReversoDocumento();

        // Preprocessar o texto
        $conteudo = strtolower(ascii($texto));
        $tokens = explode(' ', preg_replace('/[^a-z0-9 ]/', '', $conteudo));

        // Contar frequência de cada termo
        $frequencia = array_count_values($tokens);

        // Inserir ou atualizar tokens no índice reverso
        foreach ($frequencia as $token => $total) {
            if (!empty($token)) {
                $tokenProcessado = strlen($token) > 30 ? substr($token, 0, 30) : $token;

                // Verificar ou inserir o termo no índice
                $termoExistente = $IndiceReversoTermo->where('t_termo', $tokenProcessado)->first();

                $idTermo = $termoExistente['id_t'] ?? $IndiceReversoTermo->insert(['t_termo' => $tokenProcessado]);

                // Indexar o termo ao documento
                $IndiceReversoDocumento->indexar($idTermo, $documentoId, $total);
            }
        }

        return true;
    }
}
