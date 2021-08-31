<?php
class termos extends CI_model {
    function termo_form() {
        //$sx = 'Base de Dados Referencial de Livros Acadêmicos em Ciência da Informação (BRAPCI)';
        $cp = array();
        $bl = '<b><span style="color: red">!</span><span style="color: blue">Brapc</span><span style="color: red">¡</span> <span style="color: blue">L</span><span style="color: red">¡</span><span style="color: blue">vros</span><span style="color: red">!</span></b>';
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T80:3', '', 'Título da Obra', true, true));
        array_push($cp, array('$S100', '', 'Autor que autoriza a publicação (nome completo)', true, true));
        array_push($cp, array('$S100', '', 'e-mail do autor (nome completo)', true, true));
        array_push($cp, array('$C1', '', 'Concordo com os termos', true, true));
        array_push($cp, array('$B', '', 'Enviar termo por e-mail >>', false, true));
        $sx = '';
        $form = new form;
        $sx .= '<h1 style="text-align: left;">Prezado autor,</h1>' . cr();
        $sx .= '<p>A ' . $bl . ' constitui um produto inovador, surgido a partir da Base de Dados Referencial de Artigos de Periódicos em Ciência da Informação (BRAPCI). Seu principal objetivo é reunir e disponibilizar de forma centralizada os livros acadêmicos publicados em acesso aberto nas áreas da Ciência da Informação, Biblioteconomia, Arquivologia e domínios conexos, em qualquer língua, estabelecendo-se como recurso agregador da literatura científica nesses campos do conhecimento e permitindo que docentes, pesquisadores, estudantes, profissionais e a comunidade científica em geral tenha acesso a publicações especializadas, qualificadas e reconhecidas nessas áreas em um único local.</p>' . cr();
        $sx .= '<p>A ' . $bl . ' está em sua versão <b>beta</b> e pode ser acessada pelo endereço <a href="http://hdl.handle.net/20.500.11959/brapci.livros">http://hdl.handle.net/20.500.11959/brapci.livros</a>';
        //$sx .= '<p>Neste contexto, o Comitê Gestor da '.$bl.' convida o autor supra citado a disponibilizar sua obra na coleção principal da '.$bl.' Essa participação não representará qualquer ônus financeiro para os autor, ou para a '.$bl.'</p>'.cr();

        $sx .= '<p><center><img src="' . base_url('img/workflow_termo.jpg') . '"></center></p>';

        $sx .= '<h1 style="text-align: center;">Termo de autorização para indexação e disseminação de livros em acesso aberto na !Brapci Livros!</h1>' . cr();
        $sx .= $form -> editar($cp, '');

        $sx .= '<p>Este Termo de autorização refere-se exclusivamente à indexação, armazenamento e disseminação de conteúdo. Por meio deste Termo os autores interessados autorizam a ' . $bl . ' a produzir e publicar indicadores sobre as publicações mencionadas, preservando os dados originais da fonte para efeitos de citação. Todos os direitos autorais, morais e patrimoniais continuarão sendo regidos pelas respectivas políticas editoriais e institucionais de cada Programa, não cabendo à ' . $bl . ' proceder a qualquer alteração.</p>' . cr();
        $sx .= '<p>Todo o conteúdo das obras será disponibilizado em acesso aberto, incluindo os metadados. Todos os trabalhos disponibilizados obedecerão às licenças Creative Commons definidas por cada publicação, constituindo a ' . $bl . ' apenas o instrumento agregador das publicações.</p>' . cr();
        $sx .= '<p>Nos termos do presente documento, eu autor da obra autorizo a ' . $bl . ' a coletar, indexar e disseminar o conteúdo da obra.</p>' . cr();

        $sx .= '<p>A ' . $bl . ' é uma iniciativa sem fins comerciais, estruturada como um repositório que propõe coletar, organizar, disseminar e produzir indicadores sobre a literatura acadêmica publicada como livro nas áreas indicadas, desempenhando ainda papel relevante como fonte de consulta para pesquisas de graduação e de pós-graduação. O projeto nasceu em 2018 por meio da parceria entre o Programa de Pós-Graduação em Ciência da Informação da Universidade Federal do Rio Grande do Sul (PPGCI/UFRGS), onde a Brapci está sediada atualmente.</p>' . cr();

        $sx .= '<p>Atensiosamente, e agradecendo a colaboração,<p>';
        $sx .= '<b>Prof. Dr. Rene Faustino Gabriel Junior</b>
                            <br>Programa de Pós-Graduação em Ciência da Informação da Universidade Federal do Rio Grande do Sul (PPGCIN/UFRGS)<br><br>';
        $sx .= '<b>Prof. Dra. Nanci Elizabeth Oddone</b><br>Programa de Pós-Graduação em Biblioteconomia da Universidade Federal do Estado do Rio de Janeiro (PPGB/Unirio)';
        $sx .= '</p>';

        if ($form -> saved > 0) {
            $titulo = get("dd1");

            $sx = 'Email enviado para ' . get("dd2") . ' &lt;' . get("dd3") . '&gt;';
            $txtt = $this -> termo_email();
            $txt = '';
            
            // PASSO A PASSO
            $sxt = '<table border=1>';
            $sxt .= '<tr><th width="50">#</th><th>Descrição das etapas</th></tr>' . cr();
            $sxt .= '<tr><td style="font-size: 40px; text-align:center;">1</td><td style="padding:10px;">Abra o e-mail com o título “Termo de autorização para indexação, publicação e disseminação de livros acadêmicos em acesso aberto na Brapci Livros”</td></tr>' . cr();
            $sxt .= '<tr><td style="font-size: 40px; text-align:center;">2</td><td style="padding:10px;">Responda o e-mail, informando no corpo do texto “Eu concordo”</td></tr>' . cr();
            $sxt .= '<tr><td style="font-size: 40px; text-align:center;">3</td><td style="padding:10px;">Anexar a obra no e-mail</td></tr>' . cr();
            $sxt .= '<tr><td style="font-size: 40px; text-align:center;">4</td><td style="padding:10px;">Responder o e-mail para brapcici@gmail.com</td></tr>' . cr();
            $sxt .= '<tr><td colspan=2 style="padding:10px; text-align: center;">No prazo de 15 dias sua obra será disponibilizada na base de dados.</td></tr>' . cr();
            $sxt .= '</table>';            

            // EMAIL
            $txte = '';
            $txte .= '<p>Prezado '.get("dd2").',<p>';
            $txte .= '<p>Recebemos a solicitação de indexação da obra <b>'.get("dd1").'</b>.</p>';
            $txte .= '<p>Agradecemos sua participação em nosso projeto e esperamos ampliar a visibilidade de sua obra.</p>';
            $txte .= '<p>A ' . $bl . ' constitui um produto inovador, surgido a partir da Base de Dados Referencial de Artigos de Periódicos em Ciência da Informação (BRAPCI). Seu principal objetivo é reunir e disponibilizar de forma centralizada os livros acadêmicos publicados em acesso aberto nas áreas da Ciência da Informação, Biblioteconomia, Arquivologia e domínios conexos, em qualquer língua, estabelecendo-se como recurso agregador da literatura científica nesses campos do conhecimento e permitindo que docentes, pesquisadores, estudantes, profissionais e a comunidade científica em geral tenha acesso a publicações especializadas, qualificadas e reconhecidas nessas áreas em um único local.</p>' . cr();
            $txte .= '<p>Estamos na fase de coleta de obras, o site provisório está disponível em <a href="http://hdl.handle.net/20.500.11959/brapci.livros">http://hdl.handle.net/20.500.11959/brapci.livros</a>.</p>' . cr();

            $txte .= '<p>Você estará recebendo em seu e-mail o termo de autorização de indexação que deverá ser reencaminhado.</p>';
            $txte .= $sxt;
            $txte .= '<br>';
            $txte .= '<p>Atensiosamente, e agradecendo a colaboração,<p>';
            $txte .= '<b>Prof. Dr. Rene Faustino Gabriel Junior</b>
                                <br>Programa de Pós-Graduação em Ciência da Informação da Universidade Federal do Rio Grande do Sul (PPGCIN/UFRGS)<br><br>';
            $txte .= '<b>Prof. Dra. Nanci Elizabeth Oddone</b><br>Programa de Pós-Graduação em Biblioteconomia da Universidade Federal do Estado do Rio de Janeiro (PPGB/Unirio)';
            $txte .= '</p>'; 
                        
            // TEXTO
            $sx .= '<h3 style="color: green;">Envio completo</h3>';
            $sx .= '<p>A primeira etapa do processo foi finalizada, você receberá dois e-mail, um com instruções e outro com o termo de autorização que deverá ser encaminhado para a brapcici@gmail.com, tendo em anexo a obra descrita.</p>';
            $sx .= '<p>As etapas são</p>';
            $sx .= $sxt.'<br>';
            $sx .= '<p>A ' . $bl . ' é uma iniciativa sem fins comerciais, estruturada como um repositório que propõe coletar, organizar, disseminar e produzir indicadores sobre a literatura acadêmica publicada como livro nas áreas indicadas, desempenhando ainda papel relevante como fonte de consulta para pesquisas de graduação e de pós-graduação. O projeto nasceu em 2018 por meio da parceria entre o Programa de Pós-Graduação em Ciência da Informação da Universidade Federal do Rio Grande do Sul (PPGCI/UFRGS), onde a Brapci está sediada atualmente.</p>' . cr();
            
            /* ENVIA TERMO */
            $assunto = utf8_decode('Publicação de obra na BrapciLivros');
            $de = 1;
            $texto = utf8_decode($txte);
            $this->load->helper('email');
            $para = get("dd3");
            enviaremail($para, $assunto, $texto, $de);
            
            
            /* ENVIA TERMO */
            $assunto = utf8_decode('Termo de autorização para indexação, publicação e disseminação de livros acadêmicos em acesso aberto na Brapci Livros');
            $de = 1;
            $texto = utf8_decode($txtt);
            enviaremail($para, $assunto, $texto, $de);  
            
            enviaremail('brapcici@gmail.com', '[copia] '.$assunto, $para.'<hr>'.$texto, $de);       

            
        }

        return ($sx);
    }

    function termo_email() {
        $sx = '';
        $bl = '<b><span style="color: red">!</span><span style="color: blue">Brapc</span><span style="color: red">¡</span> <span style="color: blue">L</span><span style="color: red">¡</span><span style="color: blue">vros</span><span style="color: red">!</span></b>';
        $sx .= '<h1 style="text-align: center;">Termo de autorização para indexação e disseminação de livros em acesso aberto na !Brapci Livros!</h1>' . cr();
        $sx .= '<p>Eu, <b>' . get("dd2") . '</b> na qualidade de responsável pela obra "<b>' . get("dd1") . '</b>":</p>' . cr();
        $sx .= '<ol>';
        $sx .= '<li>Autorizo a ' . $bl . ' a indexar e disseminar a referida obra gratuitamente sob a licença <a href="https://creativecommons.org/licenses/by/4.0/deed.pt">Creative Commons Licença 4.0 (CC-BY)</a>.</li>';
        $sx .= '<li>Concordo que a disponibilização não terá nenhum custo a pagar ou receber para as partes.</li>';
        $sx .= '<li>Declaro que se o documento entregue é baseado em trabalho financiado ou apoiado, o responsável declara que cumpriu quaisquer obrigações exigidas pelo respectivo contrato ou acordo.</a>';
        $sx .= '</ol>';
        $sx .= '<p>A '.$bl.':';
        $sx .= '<ol>';
        $sx .= '<li>Identificará claramente o(s) seu (s) nome (s) como o (s) autor (es) ou detentor (es) dos direitos do documento entregue, e não fará qualquer alteração, para além das permitidas por esta licença;</li>';
        $sx .= '<li>Em caso de solicitação do responsável da obra, ou um de seus autores, solicitar a remoção da base, esta será feita assim que receber a solicitação.</li>';
        $sx .= '</ol>';
        $sx .= '<br>';
        $sx .= '<p>Este Termo de autorização refere-se exclusivamente à indexação, armazenamento e disseminação de conteúdo. Por meio deste Termo os autores interessados autorizam a ' . $bl . ' a produzir e publicar indicadores sobre as publicações mencionadas, preservando os dados originais da fonte para efeitos de citação. Todos os direitos autorais, morais e patrimoniais continuarão sendo regidos pelas respectivas políticas editoriais e institucionais de cada Programa, não cabendo à ' . $bl . ' proceder a qualquer alteração.</p>' . cr();
        $sx .= '<p>Todo o conteúdo das obras será disponibilizado em acesso aberto, incluindo os metadados. Todos os trabalhos disponibilizados obedecerão às licenças Creative Commons definidas por cada publicação, constituindo a ' . $bl . ' apenas o instrumento agregador das publicações.</p>' . cr();
        $sx .= '<p>Internet, '.date("d").' de '.msg('mes'.date("m")).' de '.date("Y").'.</p>' . cr();

        return ($sx);

    }

}
?>
