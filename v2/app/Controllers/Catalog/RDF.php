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
        pre($params,false); // Debug: exibe os parâmetros recebidos
        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/concept_add', $params);
    }

    public function rdf_text_add()
    {
        // Pegue os parâmetros GET
        pre("OI - text",false);
        pre($_POST, false);
        pre($_GET, false);
        exit;
        $params = $this->request->getGet() ?? $this->request->getPost() ?? [];
        pre($params); // Debug: exibe os parâmetros recebidos
        // Renderize uma view simples (ajuste o caminho conforme necessário)
        return view('catalog/rdf/text_add', $params);
    }
}
