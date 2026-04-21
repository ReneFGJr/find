<?php

namespace App\Controllers\Find\Rdf;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class Concept extends Controller
{
    use ResponseTrait;

    public function adicionar_atributo()
    {
        // Exibe a view do formulário para adicionar atributo
        return view('rdf/concept_adicionar_atributo');
    }
}
