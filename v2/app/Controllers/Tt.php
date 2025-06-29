<?php

namespace App\Controllers;

class Tt extends BaseController
{
    public function index($d1='', $d2='', $d3='')
    {
        $sx = view('Code/header');
        $dt = [];
        switch ($d1) {
            case 'item':
                $sx .= view('Code/Item/Item', $dt);
                break;
            case 'social':
                switch ($d2) {
                    case 'login':
                        return $sx . $this->socialLogin();
                    case 'logout':
                        return redirect()->to('/tt/social/login');
                    default:
                        return redirect()->to('/tt/social/login');
                }
                return $sx . $this->socialLogin();
            default:
                return redirect()->to('/tt/social/login');
        }
        return $sx;
    }

    function  socialLogin()
    {
        $sx = view('Code/Social/login', [
            'title' => 'FindServer',
            'description' => 'Faça login para acessar sua conta.',
            'keywords' => 'login, sistema, autenticação',
        ]);
        return $sx;
    }
}
