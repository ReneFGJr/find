<?php

namespace App\Controllers\Find;

use App\Controllers\BaseController;

helper('sisdoc');

class Item extends BaseController
{

    public function etiquetas_save()
    {
        $id_i = $this->request->getPost('id_i');
        $ln1 = $this->request->getPost('i_ln1');
        $ln2 = $this->request->getPost('i_ln2');
        $ln3 = $this->request->getPost('i_ln3');
        $ln4 = $this->request->getPost('i_ln4');

        if (!$id_i) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID do item não informado.']);
        }

        $data = [
            'i_ln1' => $ln1,
            'i_ln2' => $ln2,
            'i_ln3' => $ln3,
            'i_ln4' => $ln4,
        ];
        $Items = new \App\Models\Find\Items\Index();
        $updated = $Items->update($id_i, $data);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Etiqueta atualizada com sucesso!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao atualizar etiqueta.']);
        }
    }

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
                return view('find/Labels/index',$data);
                break;
        }
        echo json_encode($RSP);
        exit;
    }
}
