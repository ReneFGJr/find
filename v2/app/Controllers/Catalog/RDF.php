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
            $RDF_Class = new \App\Models\Find\Rdf\RDF_class();
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
        $RDF_Data = new \App\Models\Find\Rdf\RDF_data();
        $RDF_name = new \App\Models\Find\Rdf\RDF_name();

        /************************* Salvar  */
        $action = $this->request->getPost('action');
        if ($action != '')
            {
                $textValue = $this->request->getPost('textValue');
                $idD = $this->request->getPost('idD');

                if (is_numeric($idD) && trim((string) $textValue) !== '') {
                    $idD = (int)$idD;
                    $rdfData = $RDF_Data->find($idD);
                    $idN = (int) ($rdfData['d_literal'] ?? 0);

                    if ($idN < 1) {
                        return redirect()->back()->with('error', 'O registro selecionado não é uma literal válida para edição.');
                    }

                    // Atualiza o valor da literal
                    $RDF_name->update($idN, ['n_name' => trim((string) $textValue)]);

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
            $dataN = $RDF_name->where('id_n', $data['d_literal'])->first();
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

        $isPost = strtolower($this->request->getMethod()) === 'post';
        $params = ($isPost ? $this->request->getPost() : $this->request->getGet()) ?? [];
        $params['langs'] = $langs;
        if ($isPost) {
            $text = trim((string) ($params['textValue'] ?? ''));
            if ($text === '' || !ctype_digit((string) ($params['idC'] ?? '')) || (int) $params['idC'] < 1
                || !ctype_digit((string) ($params['prop'] ?? '')) || (int) $params['prop'] < 1
                || !in_array($params['lang'] ?? '', array_column($langs, 'lg_code'), true)) {
                $params['error'] = 'Informe o texto, o idioma e uma propriedade válida.';
            } else {
                $RDF = new \App\Models\Find\Rdf\RDF();
                $idL = $RDF->createTextLiteral($params['idC'], $params['prop'], $text, $params['lang']);
                if ($idL) {
                    return $this->response->setBody('<script>window.parent.location.reload();</script>');
                }
                $params['error'] = 'Não foi possível salvar o texto. Tente novamente.';
            }
        }

        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/text_add', $params);
    }
}
