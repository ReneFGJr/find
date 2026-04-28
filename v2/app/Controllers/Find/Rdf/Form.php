<?php
namespace App\Controllers\Find\Rdf;

use App\Controllers\BaseController;

helper('sisdoc');

class Form extends BaseController
    {
    /**
     * Adiciona um novo valor literal (n_name) para uma propriedade/conceito
     * Espera POST: c_class, form_group, n_type, form_range, n_name
     * Retorna JSON: {success: true/false, message: ''}
     */
    public function adicionar_literal()
    {
        $IDc = $this->request->getPost('idc');
        $n_name = $this->request->getPost('n_name');
        $property = $this->request->getPost('property');

        if (!$property || $n_name === null || $IDc === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parâmetros obrigatórios não informados. [property: ' . $property . ', n_name: ' . $n_name . ', idc: ' . $idc . ']',
            ]);
        }

        // Insere novo literal
        $RDF_Name = new \App\Models\Find\Rdf\RDF_Name();
        $lang = 'pt_BR'; // ou defina conforme necessário
        $DTname = $RDF_Name->where(['n_name' => $n_name, 'n_lang' => $lang])->first();
        if ($DTname) {
            $id_n = $DTname['id_n'];
        } else {
            $id_n = $RDF_Name->insert([
                'n_name' => $n_name,
                'n_lang' => $lang,
                'n_lock'=>0,

            ]);
        }
        if (!$id_n) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao inserir literal no banco de dados.'
            ]);
        }

        /**************************/
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();

        $RDF_Data->createLink($IDc, $property, 0, $id_n);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Literal adicionado com sucesso.',
        ]);
    }

    /**
     * Upload de capa de livro por ISBN
     * Salva como _covers/image/{isbn}.jpg
     */
    public function upload_cover_link()
    {
        $isbn = $this->request->getPost('isbn') ?? $this->request->getGet('isbn');
        $inputUrl = $this->request->getPost('inputUrl') ?? $this->request->getGet('inputUrl');
        if (!$isbn || !$inputUrl) {
            $msg = 'ISBN ou URL não fornecidos.';
            $data['content'] = $msg;
            return view('components/content', $data);
        } else {
            // Garante diretório
            $dir = FCPATH . '_covers/image/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $target = $dir . $isbn . '.jpg';
            $origin = $inputUrl;

            // Baixa a imagem via CURL
            $ch = curl_init($origin);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Não checa SSL
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Não checa host SSL
            $img = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($img && $http_code == 200) {
                file_put_contents($target, $img);
                $msg = 'Capa baixada e salva com sucesso.';
            } else {
                $msg = 'Erro ao baixar a imagem. Código HTTP: ' . $http_code;
            }

            $data['content'] = $msg;
            return view('components/content', $data);
        }
    }
    public function upload_cover()
    {
        helper(['filesystem', 'form']);
        $isbn = $this->request->getPost('isbn') ?? $this->request->getGet('isbn');
        if (!$isbn) {
            $data['content'] = 'ISBN não fornecido.';
            return view('components/content', $data);
        }

        $file = $this->request->getFile('coverFile');
        $id_item = $this->request->getPost('id_item');

        $msg = '';
        if (!$isbn || !$file || !$file->isValid()) {
            $msg = '<div class="alert alert-danger">ISBN ou arquivo inválido. ISBN: ' . $isbn . ', File error: ' . ($file ? $file->getErrorString() : 'No file uploaded</div>');
            $msg = '';
            return view('catalog/upload_cover', ['isbn'=>$isbn, 'msg' => $msg]);
        }

        // Garante diretório
        $dir = FCPATH . '_covers/image/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $target = $dir . $isbn . '.jpg';

        // Valida tipo
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/webp', 'image/png'])) {
            $msg = 'Apenas arquivos JPEG são permitidos. ['. $file->getMimeType().']';
            return view('catalog/upload_cover', ['isbn' => $isbn,'msg' => '<div class="alert alert-danger">' . $msg . '</div>']);
        }

        // Move arquivo
        if ($file->move($dir, $isbn . '.jpg', true)) {
            // Se veio id_item, redireciona para edição
            $dd['content'] = view('layout/header').'<div class="alert alert-success">Capa enviada com sucesso!</div>';
            return view('components/content', $dd);
        } else {
            $msg = 'Erro ao salvar o arquivo.';
            return view('catalog/upload_cover', ['isbn' => $isbn,'msg' => '<div class="alert alert-danger">' . $msg . '</div>']);
        }
    }

    /**
     * Salva o campo form_range de um formulário RDF
     * Espera POST: id_form, form_range (JSON array)
     */
    public function salvar_range()
        // Debug: mostrar dados antes de salvar
    {
        // Suporte a JSON puro no corpo da requisição
        $data = $this->request->getJSON(true);
        $id_form = $data['id_form'] ?? $this->request->getPost('id_form');
        $form_range = $data['form_range'] ?? $this->request->getPost('form_range');
        // Decodifica se vier como JSON string
        if (is_string($form_range)) {
            $decoded = json_decode($form_range, true);
            if (is_array($decoded)) {
                $form_range = json_encode($decoded); // Salva como JSON string
            }
        }

        // (Removido debug JS para garantir resposta JSON válida)
        $model = new \App\Models\Find\Rdf\RDF_Form();
        $msg = '';
        if ($id_form < 1) {
            $msg = 'ID do formulário inválido. ['.$id_form.']';
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        } // Validação extra para ID inválido


        $ok = $model->update($id_form, ['form_range' => $form_range]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao salvar no banco de dados. '.$msg]);
        }
    }

    /**
     * Remove um registro único da tabela rdf_data
     * Espera POST: id_d
     * Retorna JSON: {success: true/false, message: ''}
     */
    public function excluirRdfData()
    {
        $id_d = $this->request->getPost('id_d');
        if (!$id_d) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID não informado.'
            ]);
        }
        $RDF_Data = new \App\Models\Find\Rdf\RDF_Data();
        $deleted = $RDF_Data->delete($id_d);
        if ($deleted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registro removido com sucesso.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao remover registro.'
            ]);
        }
    }

    public function index($id = null)
    {
        $rdfForm = new \App\Models\Find\Rdf\RDF_Form();
        $rdfModel = new \App\Models\Find\Rdf\RDF();

        // Buscar conceito e propriedades reais pelo ID
        $conceptData = $rdfModel->le($id);
        $concept = $conceptData['concept'] ?? [];
        $data = $conceptData['data'] ?? [];

        $Expression = [];
        $Manifestation = [];
        // Pega a biblioteca selecionada do cookie
        if (!function_exists('get_cookie')) {
            helper('cookie');
        }
        $library = get_cookie('library_code') ?: get_cookie('library');

        /******************************* From Type */
        $Class = $concept['Class'] ?? null;

        $dataForm = [];
        switch ($Class) {
            case 'Work': // Work
                $dataForm['Work'] = $rdfForm->getForm('W', $id, $library);
                break;
            case 'Expression': // Expression
                $dataForm['Expression'] = $rdfForm->getForm('E', $id, $library);
                break;
            case 'Manifestation': // Manifestation
                $dataForm['Manifestation'] = $rdfForm->getForm('M', $id, $library);
                break;
        }

        return view('find/rdf/form/rdf_edit_concept', [
            'concept' => $concept,
            'id'=>$id,
            'dataForm' => $dataForm,
            'data' => $data
        ]);
    }

    public function salvar_literal()
    {
        $data = $this->request->getJSON(true);
        $id_n = $data['id_n'] ?? null;
        $n_name = $data['n_name'] ?? null;
        if (!$id_n || $n_name === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parâmetros obrigatórios não informados.'
            ]);
        }
        $model = new \App\Models\Find\Rdf\RDF_Name();

        /************************* Atualização */
        $ok = $model->update($id_n, ['n_name' => $n_name]);
        if ($ok) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar no banco de dados.'
            ]);
        }
    }
}
