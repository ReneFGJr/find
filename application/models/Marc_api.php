<?php
class Marc_api extends CI_model
{
	function book($t)
	{
		$type = "MARC2";
		$t = troca($t,';','.,');
		$t = troca($t,chr(13),';');
		$t = troca($t,chr(10),';');
		$ln = splitx(';',$t);
		$w = array();
		$w['authors'] = array();
		$w['cover'] = '';
		$w['subject']= array();
		$w['descricao'] = '';
		$w['expressao'] = array('genere'=>'books','idioma'=>'pt');
		$s = '';
		for ($r=0;$r < count($ln);$r++)
		{
			$l = $ln[$r];

			$field = substr($l,0,3);
			switch ($field) 
			{
				/***************** ISBN ***********/
				case '020':
				$sr = $this->extract($l,'a');
				if (strpos($sr,'(')) 
				{
					$sr = trim(substr($sr,0,strpos($sr,'(')));
				}
				$w['isbn'] = $this->isbn->isbns($sr);
				break;

				/************************* Idioma */
				case '041':
				$sr = $this->extract($l,'a');
				$w['expressao'] = array('genere'=>'books','idioma'=>$sr);
				break;				

				/***************** AUTORES ***********/
				case '100':
				$sr = $this->extract($l,'a');
				$sr = nbr_author($sr,8);
				array_push($w['authors'],$sr);
				break;

				case '700':
				$sr = $this->extract($l,'a');
				$sr = nbr_author($sr,8);
				array_push($w['authors'],$sr);
				break;

				/***************** Título ***********/
				case '245':
				$sr = $this->extract($l,'a');
				$title = $sr;
				$w['title'] = $sr;
				break;

				/***************** Editora ***********/
				case '260':
				$sr = $this->extract($l,'b');
				$w['editora'] = $sr;

				$sr = $this->extract($l,'c');
				$w['data'] = $sr;

				break;					

				/***************** Pages ***********/
				case '300':
				$sr = $this->extract($l,'a');
				$sr = troca($sr, 'p.','');
				if (strpos($sr,',') > 0)
				{
					$w['pages_pre'] = substr($sr,0,strpos($sr,','));
				}
				$w['pages'] = sonumero($sr);
				break;				

				/***************** SERIE ***********/
				case '490':
				$sr = $this->extract($l,'a');
				$sr = troca($sr,'(','');
				$sr = troca($sr,')','');
				$sr = nbr_author($sr,8);
				$w['serie'] = $sr;

				$sr = $this->extract($l,'v');
				$sr = troca($sr,'(','');
				$sr = troca($sr,')','');
				$w['volume'] = 'v. '.$sr;
				break;	

				/***************** Assuntos ***********/
				case '650':
				$sr = $this->extract($l,'a');
				$srx = $this->extract($l,'x');
				if (strlen($srx) > 0)
					{ $sr .= ' - '.$srx; }
				array_push($w['subject'],$sr);
				break;							

				default:
							# code...
				break;
			}

			$s .= ($r+1).'. ';
			$s .= $ln[$r];
			$s .= cr();
		}

		$w['error'] = 0;
		$w['type'] = $type;

		if (strlen($title) > 0)
		{
			$w['error_msg'] = 'ISBN_inported';
			$w['totalItems'] = 1;
			$w['error'] = 0;
			$w['url'] = $this->isbn->vurl;
		} else {
			$w['error'] = 1;
			$w['error_msg'] = 'ISBN_not_found';
			$w['totalItems'] = 0;
			$w['url'] = '';
		}	
		return($w);
	}
	function extract($t,$sub)
	{
		$t = troca($t,'|','$');
		if (strpos($t,'$'.$sub) > 0)
		{
			$t = trim(substr($t,strpos($t,'$'.$sub)+2,strlen($t)));
			if (strpos($t,'$') > 0)
			{
				$t = trim(substr($t,0,strpos($t,'$')));
			}


			$cut = 1;
			while ($cut == 1)
			{
				$last = substr($t,strlen($t)-1,1);			
				switch($last)
				{
					case '/':
					$cut = 1;
					break;

					case ',':
					$cut = 1;
					break;

					case '-':
					$cut = 1;
					break;	

					case ':':
					$cut = 1;
					break;	

					case '.':
					$cut = 1;
					break;

					default:
					$cut = 0;										
				}
				if ($cut == 1)
				{
					$t = trim(substr($t,0,strlen($t)-1));
				}
			}			
		} else {
			$t = '';
		}
		return($t);
	}
	function form()
	{
		$sx = '';
		$sx .= '<div>';
		$sx .= '<span class="btn btn-outline-primary" onclick="marc_show();">Marc21</span">';
		$sx .= '</div>';

		$sx .= '<div id="marc_form" style="display: none">';
		$form = new form;
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$T80:10','',msg('marc_code_import'),true,true));
		array_push($cp,array('$B8','',msg('import'),false,false));
		$tela = $form->editar($cp,'');
		$sx .= $tela;
		$sx .= msg('marc_insert_text');
		$sx .= '</div>';



		$sx .= '<script>';
		$sx .= ' function marc_show() { ';
		$sx .= '	$("#marc_form").toggle("slow");';
		$sx .= ' } ; ';
		$sx .= '</script>'.cr();
		return($sx);
	}
}
/*
000 01026cam a22003137  4500
001 000329484
003 BR-RjBN
005 20150119123544.0
008 130625s2012    rjb    g      000 0 por  
020 __ |a 9788521804888 (broch.)
035 __ |a 2013062512392620med
040 __ |a BR-RjBN |b por
041 1_ |a por |h fre
043 __ |a e-fr---
082 04 |a 194 |2 22
092 __ |a ANEXO II-992,1,25
100 1_ |a Foucault, Michel, |d 1926-1984
240 00 |a Dits et écrits. |l Português
245 10 |a Ética, sexualidade, política / |c Michel Foucault ; organização, seleção de textos e revisão técnica: Manoel Barros da Motta ; tradução: Elisa Monteiro, Inês Autran Dourado Barbosa. -
250 __ |a 3. ed. -
260 __ |a Rio de Janeiro : |b Forense Universitária, |c 2012.
300 __ |a lxiii, 325p. ; |c 22cm. -
490 __ |a (Ditos & escritos |v 5)
500 __ |a Tradução de: Dits et écrits.
595 __ |a BNB |c 03/13
650 04 |a Filosofia francesa-
700 1_ |a Motta, Manoel Barros da
852 __ |a Obras Gerais
949 __ |a 1.392.166 DL 28/06/2013
990 __ |a Livro
*/
?>	