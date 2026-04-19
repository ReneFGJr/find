<?php
namespace App\Models\Find\Items;

use CodeIgniter\Model;

/**
 * Model para processar metadados de livros sem usar tabela no banco.
 * Pode ser usado para parse, validação ou transformação de dados vindos de APIs externas.
 */
class ProcessMetadata extends Model
{
    // Não usa tabela
    protected $table = '';
    protected $primaryKey = '';
    protected $allowedFields = [];

    /**
     * Recupera o título do array de resultado Z39.50
     * @param array $z3950_result
     * @return string|null
     */
    public function getTitleFromZ3950Result($z3950_result)
    {
        if (!isset($z3950_result['data']) || !is_array($z3950_result['data'])) {
            return null;
        }
        foreach ($z3950_result['data'] as $item) {
            if (isset($item['property']) && $item['property'] === 'hasTitle' && isset($item['literal'])) {
                return $item['literal'];
            }
        }
        return null;
    }

    public function processZ3950Result($z3950_result, $isbn = null)
    {
        $RSP = [];
        $Item = new \App\Models\Find\Items\Index();
        $RDF = new \App\Models\Find\Rdf\RDF();
        // Exemplo de processamento específico para resultado Z39.50
        $titulo = $this->getTitleFromZ3950Result($z3950_result);
        if ($titulo) {
            // Atualiza se i_titulo for null ou string vazia
            $dd = [];
            $dd['i_titulo'] = $titulo;
            $RSP[] = "Título atualizado para ISBN $isbn: $titulo";
        }

        /************************************************** WORK */
        $wName = 'ISBN:'.$isbn;
        $Work = $RDF->createConcept('Work', $wName);

        $dd['i_work'] = $Work['id_cc'];
        $RSP[] = "ID do Work criado para ISBN $isbn: " . $Work['id_cc'];

        /******************************************** Expression */


        if ($dd != []) {
            $Item->set($dd)
                ->where('i_identifier', $isbn)
                ->groupStart()
                ->where('i_titulo', null)
                ->orWhere('i_titulo', '')
                ->orWhere('i_work', 0)
                ->groupEnd()
                ->update();
        }

        // ... outros processamentos ...
        return $RSP;
    }

    /**
     * Exemplo de método para processar metadados de um livro
     * @param array $metadata
     * @return array
     */
    public function process(array $metadata): array
    {
        // Exemplo: normalizar campos, validar, transformar...
        $result = [];
        if (isset($metadata['isbn'])) {
            $result['isbn'] = preg_replace('/[^0-9Xx]/', '', strtoupper($metadata['isbn']));
        }
        if (isset($metadata['titulo'])) {
            $result['titulo'] = trim($metadata['titulo']);
        }
        if (isset($metadata['autor'])) {
            $result['autor'] = trim($metadata['autor']);
        }
        // Adicione outras regras conforme necessário
        return $result;
    }
}
