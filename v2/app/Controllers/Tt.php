<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Tt extends BaseController
{
    public function index($d1='',$d2='',$d3='',$d4='')
    {
        $sx = view('headers/header');
        $sx .= view('widgets/logo_find');
        $sx .= view('widgets/system_version');

        $RSM1 = new \App\Models\Painel\Rsm01();
        $RSM2 = new \App\Models\Painel\Rsm02();

        $sx .= '<div class="container"><div class="row">';
        $sx .= '<div class="col-md-2 col-12">';
        $sx .= view('widgets/painel/p1', ['content'=>$RSM1->index($d1,$d2,$d3,$d4)]);
        $sx .= '</div>';

        $sx .= '<div class="col-md-2 col-12">';
        $sx .= view('widgets/painel/p1', ['content' => $RSM2->index($d1, $d2, $d3, $d4)]);
        $sx .= '</div>';

        $sx .= '</div></div>';
        return $sx;
    }
}
