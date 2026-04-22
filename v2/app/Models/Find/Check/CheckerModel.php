<?php
namespace App\Models\Find\Check;

helper('sisdoc');

class CheckerModel {
    /**
     * Checa a tabela find_item e preenche autores/título usando RDF se necessário.
     * @return array Lista de arrays com i_work, i_manifestation, hasTitle, hasAuthor
     */
    public function checkFindItem($id=0)
    {
        $result = [];
        // Usa o Model para buscar itens sem título ou autores
        $itemModel = new \App\Models\Find\Items\Index();
        $items = $itemModel
            ->select('id_i, i_work, i_expression, i_manifestation, i_identifier')
            ->groupStart()
                ->where('i_titulo', '')
                ->where('i_work >', 0)
            ->groupEnd()
            ->findAll(100);

            echo "Itens encontrados: " . count($items) . '<br>';

        $rdf = new \App\Models\Find\Rdf\RDF();
        foreach ($items as $item) {
            $i_work = $item['i_work'];
            $i_expression = $item['i_expression'];
            $i_manifestation = $item['i_manifestation'];
            $dados = [];
            $dados1 = [];
            $dados2 = [];
            $dados3 = [];
            if (!$i_manifestation) continue;

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

            foreach ($dados as $d) {
                $prop = $d['Property'];
                // Identifica o work (Work)
                switch($prop)
                    {
                        case 'prefLabel':
                            $Title = $d['Caption'];
                            break;
                        case 'hasTitle':
                            $Title = $d['Caption'];
                            break;
                        case 'hasAuthor':
                            if ($Author != '') {
                                $Author .= '; ';
                            }
                            $Author .= $d['Caption'];
                            break;
                    }
            }

            if ((strlen($item['i_identifier']) > 10) and ($Title != '')) {
                echo "==" . $Title . '<br>';
                echo "==" . $Author . '<br>';

                $dd['i_titulo'] = $Title;
                $dd['i_autores'] = $Author;

                $itemModel->set($dd)->where('i_identifier', $item['i_identifier'])->update();
                echo $itemModel->getLastQuery() . '<br>';
                pre($dd);
                exit;
            }
        }
        return $result;
    }
}
