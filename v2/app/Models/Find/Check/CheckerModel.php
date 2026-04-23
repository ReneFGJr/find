<?php

namespace App\Models\Find\Check;

helper(['sisdoc','nbr']);

class CheckerModel
{
    /**
     * Checa a tabela find_item e preenche autores/título usando RDF se necessário.
     * @return array Lista de arrays com i_work, i_manifestation, hasTitle, hasAuthor
     */
    public function checkFindItem($id = 0)
    {
        $result = [];
        // Usa o Model para buscar itens sem título ou autores
        $itemModel = new \App\Models\Find\Items\Index();
        $items = $itemModel
            ->select('id_i, i_work, i_expression, i_manifestation, i_identifier')
            ->groupStart()
            ->where('i_titulo', null)
            ->orWhere('i_titulo', '')
            ->groupEnd()
            ->where('i_work >', 0)
            ->findAll(1000);

        $rsp = "Itens encontrados: " . count($items) . '<br>';

        foreach ($items as $item) {

            $this->updateDataTitleAuthor($item);
            $data['content'] = '<tt>' . $rsp . '</tt>';
            return view('components/content', $data);

            return $result;
        }
    }

    function updateDataTitleAuthor($item)
    {
        //set_time_limit(0);
        $rdf = new \App\Models\Find\Rdf\RDF();
        $itemModel = new \App\Models\Find\Items\Index();

        $i_work = $item['i_work'];
        $i_expression = $item['i_expression'];
        $i_manifestation = $item['i_manifestation'];
        $dados = [];
        $dados1 = [];
        $dados2 = [];
        $dados3 = [];
        //if (!$i_manifestation) continue;

        // Lê dados RDF da manifestação
        if ($i_work > 0) {
            $dados1 = $rdf->le($i_work);
            $dados = array_merge($dados, $dados1['data']);
        }
        if ($i_expression > 0) {
            $dados2 = $rdf->le($i_expression);
            $dados = array_merge($dados, $dados2['data']);
        }
        if ($i_manifestation > 0) {
            $dados3 = $rdf->le($i_manifestation);
            $dados = array_merge($dados, $dados3['data']);
        }

        $Title = '';
        $Author = '';

        $rsp = "<li>Processando i_work: " . $i_work . "</li>";

        foreach ($dados as $d) {
            $prop = $d['Property'];
            // Identifica o work (Work)
            if (!isset($d['Caption'])) continue;
            if (is_null($d['Caption'])) continue;

            $txt = trim($d['Caption']);

            if ($txt == '') continue;
            switch ($prop) {
                case 'prefLabel':
                    $Title = $txt;
                    break;
                case 'hasTitle':
                    $Title = $txt;
                    break;
                case 'hasAuthor':
                    if ($Author != '') {
                        $Author .= '; ';
                    }
                    $Author .= $txt;
                    break;
            }
        }

        if ($Title == '') {
            $rsp .= "<span style='color: red;'>Título não encontrado para i_work: " . $i_work . "</span><br>";
        } else {
            $rsp .= "<span style='color: green;'>Título encontrado para i_work: " . $i_work . " - " . $Title . "</span><br>";
        }

        if ((strlen($item['i_identifier']) > 10) and ($Title != '')) {

            $Title = nbr_title($Title);

            $dd['i_titulo'] = $Title;
            $dd['i_autores'] = $Author;

            if (($item['i_titulo'] != $Title) or ($item['i_autores'] != $Author) or ($item['i_work'] != $i_work)) {
                $itemModel->set($dd)->where('i_identifier', $item['i_identifier'])->update();
                $rsp .= "<span style='color: blue;'>Item atualizado para i_identifier: " . $item['i_identifier'] . "</span><br>";
                return $rsp;
            }
            return "";
        }
    }
}
