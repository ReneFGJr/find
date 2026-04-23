<?php

namespace App\Controllers\Find;

use App\Controllers\BaseController;

helper('sisdoc');

class Item extends BaseController
{

    public function change_status($id_i = '')
    {
        $dd = [];
        $dd['i_status'] = $this->request->getGet('status');
        $Items = new \App\Models\Find\Items\Index();
        $RSP = $Items->update($id_i, $dd);
        return redirect()->to('/catalog/catalogar/metadadoSearch/' . urlencode($id_i));
    }
    public function etiquetas($d2 = '', $d3 = '')
    {
        helper('cookie');
        $data = [];

        $library = get_cookie('library') ?: get_cookie('library_code');
        // pre($_COOKIE); // Removido para produção, descomente para debug
        if (!$library) {
            return redirect()->to('/bibliotecas');
        }

        $Labels = new \App\Models\Find\Labels\Index();
        $Labels->setLibrary($library);

        $tombo = $this->request->getPost('tomboID');
        if ($tombo) {
            $data['messages'] =$Labels->setToPrint();
        }

        $d2 = strtoupper($d2);
        switch($d2)
        {
            case 'Z':
                $RSP = $Labels->zerar();
                break;
            case 'R':
                $RSP = $Labels->getLabels(2);
                break;
            case 'G':
                $RSP = $Labels->setToPrint(0);
                break;
            case 'P':
                $RSP = $Labels->print($d2, $d3);
                break;
            default:
                $data['nr_etiquetas'] = count($Labels->getLabels(2));
                return view('Find/Labels/index',$data);
                break;
        }
        echo json_encode($RSP);
        exit;
    }
}
