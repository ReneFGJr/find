<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BiblioFind\Works;
use App\Models\BiblioFind\IndiceReverso;

helper('sisdoc');


class BiblioFind extends BaseController
{
    public function index()
    {
        $user = user();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Você precisa estar logado para acessar o dashboard.');
        }

        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('widgets/system_version');
        $sx .= view('widgets/bibliofind_header');
        $sx .= view('headers/menu_tools');
        $sx .= view('widgets/bibliofind/bibliofind_search');

        return $sx;
    }

    public function zerar()
    {
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('widgets/system_version');
        $sx .= view('widgets/bibliofind_header');
        $sx .= view('headers/menu_tools');

        $IndiceReverso = new \App\Models\BiblioFind\IndiceReverso();
        $sx .= bs(bsc(bsmessage('Base zerada com sucesso',3)));
        //$sx .= $IndiceReverso->truncate();
        return $sx;
    }

    public function reindex()
        {
            $sx = view('headers/header');
            $sx .= view('widgets/logo_find');
            $sx .= view('widgets/system_version');
            $sx .= view('widgets/bibliofind_header');
            $sx .= view('headers/menu_tools');
            $WORKS = new Works();
            $sx .= $WORKS->indexar();
            return $sx;
        }

    public function buscar()
    {
        helper('bootstrap');
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');

        // Receber o dado do formulário
        $nomeDaObra = $this->request->getVar('term');

        // Validar o input
        if (!$nomeDaObra || strlen($nomeDaObra) < 3) {
            return redirect()->back()->with('msg', 'Por favor, insira pelo menos 3 caracteres para buscar.');
        }

        // Instanciar o modelo
        $IR = new IndiceReverso();
        $resultados = $IR->search($nomeDaObra);

        // Verificar se existem resultados
        if (empty($resultados)) {
            return redirect()->back()->with('msg', 'Nenhuma obra encontrada com o nome informado.');
        }

        // Enviar os resultados para a view
        $sx .= view('widgets/system_version');
        $sx .= view('widgets/bibliofind_header');
        $sx .= view('headers/menu_tools');
        $sx .= view('widgets/bibliofind/bibliofind_search');
        $sc = view('widgets/bibliofind/bibliofind_results', ['data' => $resultados]);
        $sx .= bs(bsc($sc,12));
        return $sx;
    }
}
