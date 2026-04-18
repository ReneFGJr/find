<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Faq;

class PagesController extends BaseController
{
    public function about()
    {
        return view('about');
    }

    public function faq()
    {
        $faqModel = new Faq();
        $faqs = $faqModel->listActive();
        return view('faq', ['faqs' => $faqs]);
    }

    public function contact()
    {
        $enviado = false;
        if ($this->request->getMethod() === 'post') {
            $nome = $this->request->getPost('nome');
            $email = $this->request->getPost('email');
            $mensagem = $this->request->getPost('mensagem');
            $to = 'brapcici@gmail.com';
            $subject = 'Contato via FIND';
            $body = "Nome: $nome\nE-mail: $email\nMensagem:\n$mensagem";
            $headers = "From: $email\r\nReply-To: $email";
            mail($to, $subject, $body, $headers);
            $enviado = true;
        }
        return view('contact', ['enviado' => $enviado]);
    }
}
