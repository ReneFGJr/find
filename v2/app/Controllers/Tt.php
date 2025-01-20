<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User\User;

class Tt extends BaseController
{
    public function index($d1='',$d2='',$d3='',$d4='')
    {

        $user = user();

        if (!$user) {
            return redirect()->to('/login')->with('msg', 'Você precisa estar logado para acessar o dashboard.');
        }

        $sx = view('headers/header');

        $sc = '';
        $sc .= view('widgets/logo_find');
        $sc .= view('widgets/system_version');

        $RSM1 = new \App\Models\Painel\Rsm01();
        $RSM2 = new \App\Models\Painel\Rsm02();

        $sc .= '<div class="container"><div class="row">';
        $sc .= '<div class="col-md-2 col-12">';
        $sc .= view('widgets/painel/p1', ['content'=>$RSM1->index($d1,$d2,$d3,$d4)]);
        $sc .= '</div>';

        $sc .= '<div class="col-md-2 col-12">';
        $sc .= view('widgets/painel/p1', ['content' => $RSM2->index($d1, $d2, $d3, $d4)]);
        $sc .= '</div>';

        $sc .= '</div></div>';

        $sx .= view('headers/menu_left', ['content' => $sc, 'user' => $user]);

        return $sx;
    }
}
