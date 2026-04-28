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

                    return redirect()->back()->with('success', 'Literal editada com sucesso!');
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
            pre($idL);
            return redirect()->back()->with('success', 'Literal adicionada com sucesso!');
        }

        pre($params, false); // Debug: exibe os parâmetros recebidos
        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/text_add', $params);
    }
}
