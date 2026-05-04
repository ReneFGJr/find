<?php
namespace App\Controllers\Catalog;

use App\Controllers\BaseController;

helper('sisdoc');

class RDF extends BaseController
{
    /**
     * Exibe o formulário para adicionar conceito (atributo)
     * GET /catalog/rdf/concept_add
     */
    public function rdf_concept_add()
    {
        pre("OI - concept",false);
        $params = $this->request->getGet() ?? $this->request->getPost() ?? [];

        $rangeRaw = $params['range'] ?? [];
        if (is_string($rangeRaw)) {
            $decoded = json_decode($rangeRaw, true);
            if (is_array($decoded)) {
                $rangeList = $decoded;
            } else {
                $tmp = str_replace(['[', ']', '"', "'"], '', $rangeRaw);
                $tmp = str_replace(['|', ';'], ',', $tmp);
                $rangeList = array_map('trim', explode(',', $tmp));
            }
        } elseif (is_array($rangeRaw)) {
            $rangeList = $rangeRaw;
        } else {
            $rangeList = [];
        }

        $rangeList = array_values(array_filter(array_map('strval', $rangeList), static function ($v) {
            return trim($v) !== '';
        }));

        $params['range'] = $rangeList;

        $rangeClassNames = [];
        if (!empty($rangeList)) {
            $RDF_Class = new \App\Models\Find\Rdf\RDF_Class();
            foreach ($rangeList as $entry) {
                if (is_numeric($entry)) {
                    $row = $RDF_Class->select('c_class')->where('id_c', (int) $entry)->first();
                    if (!empty($row['c_class'])) {
                        $rangeClassNames[] = $row['c_class'];
                    }
                } else {
                    $row = $RDF_Class->select('c_class')->where('c_class', $entry)->first();
                    $rangeClassNames[] = !empty($row['c_class']) ? $row['c_class'] : $entry;
                }
            }
        }
        $params['rangeClassNames'] = array_values(array_unique(array_filter($rangeClassNames)));

        //pre($params,false); // Debug: exibe os parâmetros recebidos
        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/concept_add', $params);
    }

    public function rdf_text_edit()
    {
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();
        $RDF_Name = new \App\Models\Find\Rdf\RDF_Name();

        /************************* Salvar  */
        $action = $this->request->getPost('action');
        echo '===>'.$action;
        if ($action != '')
            {
                $textValue = $this->request->getPost('textValue');
                $idD = $this->request->getPost('idD');
                $idN = $this->request->getPost('idN');

                if (is_numeric($idD) && is_numeric($idN)) {
                    $idD = (int)$idD;
                    $idN = (int)$idN;

                    // Atualiza o valor da literal
                    $RDF_Name->update($idN, ['n_name' => $textValue]);

                    return $this->response->setBody('<script>window.parent.location.reload();</script>');
                } else {
                    return redirect()->back()->with('error', 'IDs inválidos para edição da literal.');
                }
            }
        $id = $this->request->getGet('idD');
        if (!$id) {
            $id = $this->request->getPost('idD');
        }
        if (is_numeric($id)) {
            $id = (int)$id;
            $data = $RDF_Data->find($id);
            if ($data['d_literal'] == null) {
                return redirect()->back()->with('error', 'O registro selecionado não é uma literal válida para edição.');
            }
            $dataN = $RDF_Name->where('id_n', $data['d_literal'])->first();
            $textValue = $dataN['n_name'] ?? '';
            $lang = $dataN['n_lang'] ?? '';
            $idD = $id;
            $id_n = $dataN['id_n'] ?? '';
            return view('catalog/rdf/text_edit', compact('textValue', 'lang', 'idD', 'id_n'));
        } else {
            return redirect()->back()->with('error', 'ID inválido para edição da literal.');
        }
    }

    public function rdf_text_add()
    {
        // Pegue os parâmetros GET
        $LanguageModel = new \App\Models\Language\LanguageModel();
        $langs = $LanguageModel->getAllLanguages();

        $params = $this->request->getGet() ?? [];
        if ($params == []) $params = $this->request->getPost() ?? [];
        $params['langs'] = $langs;

        if (isset($params['textValue']) && (!empty($params['textValue'])) && isset($params['idC']) && isset($params['prop']) && isset($params['lang']) && $params['lang'] != '')
        {
            // Aqui você pode processar o valor da literal, por exemplo, salvando no banco de dados
            $RDF = new \App\Models\Find\Rdf\RDF();
            $idL =  $RDF->createLiteral($params['idC'], $params['prop'], $params['textValue'], $params['lang'] ?? '');

            return $this->response->setBody('<script>window.parent.location.reload();</script>');
        }

        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/text_add', $params);
    }
}
