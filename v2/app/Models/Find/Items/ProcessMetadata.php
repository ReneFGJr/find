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


    public function updateTitle($idW, $title, $isbn)
    {
        $RDF = new \App\Models\Find\Rdf\RDF();
        $RDF_name = new \App\Models\Find\Rdf\RDF_name();
        $idTitulo = $RDF_name->createLiteral($title);

        /***** Checa propriedade */
        $data = $RDF->le($idW);
        $idDW = 0;

        foreach ($data['data'] as $row) {
            if (isset($row['property']) && $row['property'] === 'hasTitle') {
                $idDW = $row['id_dw'];
                break;
            }
        }

        if ($idDW == 0) {
            $RDF->createLiteral($idDW, 'hasTitle', $idTitulo);
        } else {
            echo "Já existe";
        }
        return $idDW;
    }

    public function processNoISBN(string $title, $isbn = null)
        {
            $data = [];
            $data['data']['gasTitle'] = $title;
            $this->processZ3950Result($data, $isbn);
        }

    public function processZ3950Result($z3950_result, $isbn = null)
    {
        $RSP = [];
        $Item = new \App\Models\Find\Items\Index();
        $RDF_name = new \App\Models\Find\Rdf\RDF_name();
        $RDF = new \App\Models\Find\Rdf\RDF();
        $RDFdata = new \App\Models\Find\Rdf\RDF_data();

        // Exemplo de processamento específico para resultado Z39.50
        $titulo = $this->getTitleFromZ3950Result($z3950_result);

        if ($titulo) {
            // Atualiza se i_titulo for null ou string vazia
            $dd = [];
            $dd['i_titulo'] = $titulo;
            $RSP[] = "Título atualizado para ISBN $isbn: $titulo";
        }

        /************************************************** WORK */
        $wName = 'ISBN:' . $isbn;
        $Work = $RDF->createConcept('Work', $wName);

        $dd['i_work'] = $Work['id_cc'];
        $RSP[] = "ID do Work criado para ISBN $isbn: " . $Work['id_cc'];


        /************************************************** Expression */
        if (isset($z3950_result['concept']['lang'])) {
            $lang = substr($z3950_result['concept']['lang'], 0, 2);
        } else {
            $lang = 'pt';
        }
        $eName = 'ISBN:' . $isbn . ':' . $lang;
        $Expression = $RDF->createConcept('Expression', $eName);

        $dd['i_expression'] = $Expression['id_cc'];
        $RSP[] = "ID da Expression criada para ISBN $isbn: " . $Expression['id_cc'];

        /************************************************** Expression */
        $mName = 'ISBN:' . $isbn . ':book' ;
        $Manifestation = $RDF->createConcept('Manifestation', $mName);

        $dd['i_manifestation'] = $Manifestation['id_cc'];
        $RSP[] = "ID da Manifestation criada para ISBN $isbn: " . $Manifestation['id_cc'];


        /************************************************** Incluir Título (TEXT) */
        if ($titulo) {
            $idTitulo = $RDF->createLiteral($dd['i_work'], 'hasTitle', $titulo);
            $RSP[] = "Título adicionado para ISBN $isbn: $titulo (id_literal: $idTitulo)";
        }

        /************************************************** Incluir Autores (TEXT) */
        $dd['i_autores'] = '';
        if (!empty($z3950_result['data']) && is_array($z3950_result['data'])) {
            foreach ($z3950_result['data'] as $item) {

                if (isset($item['property']) && $item['property'] === 'hasAuthor' && isset($item['literal'])) {
                    $authorName = $item['literal'];
                    $aName = nbr_author($authorName,7);
                    $Author = $RDF->createConcept('Person', $aName);
                    // Linkar Work ao Author

                    $RDFdata->createLink($Work['id_cc'], 'hasAuthor', $Author['id_cc'], 0);
                    $RSP[] = "Autor adicionado para ISBN $isbn: $authorName";
                    if (strlen($dd['i_autores']) > 0) {
                        $dd['i_autores'] .= '; ';
                    }
                    $dd['i_autores'] .=  $authorName;
                }
            }

            /************************************************** Incluir Titulo (TEXT) */
            $this->updateTitle($Work['id_cc'], $titulo, $isbn);
        }

        /******************************************** Expression */
        if ($dd != []) {
            $Item->set($dd)
                ->where('i_identifier', $isbn)
                ->groupStart()
                ->where('i_titulo', null)
                ->orWhere('i_titulo', '')
                ->orWhere('i_work', 0)
                ->orWhere('i_expression', 0)
                ->orWhere('i_manifestation', 0)
                ->groupEnd()
                ->update();
            //echo $Item->getLastQuery();
        }

        // ... outros processamentos ...
        return $z3950_result;
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
