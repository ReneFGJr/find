<?php

namespace App\Models\API;

use CodeIgniter\Model;

class Marc21 extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'marc21s';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

function manual($id)
	{
		$form = new form;
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$T80:4','',msg('title'),true,true));
		$lang = $this->languages->form();
		array_push($cp,array('$O '.$lang,'',msg('language'),true,true));
		$sx = $form->editar($cp,'');
		if ($form->saved > 0)
		{
			$item = $this->books_item->le_item($id);
			$isbn = $item['i_identifier'];
			if (strlen($isbn) == 13)
			{
				$marc = '245 $a '.get("dd1");
				$dt = $this->marc_api->book($marc);
				$dt['item'] = $id;
				$this->marc_api->save_marc($isbn,$marc);
				$d = $this->books->process_register($isbn,$dt,'MARC2');
				redirect(base_url(PATH.MODULE.'v/'.$d));
			}
		}
		return($sx);
	}

	function import($t)
		{
			$file = $t;
			$sx = '';
			$marcs = array();
			$itens = array();
			if (file_exists($file))
				{
					$txt = file_get_contents($file).cr().'###########'.cr();
					
					$txt = troca($txt,chr(13),chr(10));
					$txt = troca($txt,chr(10).chr(10),chr(10));
					$ln = explode(chr(10),$txt);

					if ($ln[0] == '### START')
						{
							$marc = '';
							for ($r=1;$r < count($ln);$r++)
								{
									$l = $ln[$r];
									if ((substr($l,0,7) == '### NEW') or (substr($l,0,7) == '### FIN'))
										{
											array_push($marcs,$marc);
											$marc = '';
										} else {
											$marc .= $l.cr();
										}
								}

						} else {
							$sx .= message('ERRO NO FORMATO DO ARQUIVO',3);
						}

				} else {
					$sx .= message('ERRO NO ARQUIVO',3);
				}


				/* Processar */
				$rdf = new rdf;
				for ($r=0;$r < count($marcs);$r++)
					{
						$mc = $marcs[$r];
						if (strpos($mc,'951 ## $a') > 0)
							{
								$idt = substr($mc,strpos($mc,'951 ## $a')+10,20);
								$idt = substr($idt,0,strpos($idt,chr(10)));
								$idt = sonumero($idt);
								$tombo = round(substr($idt,4,strlen($idt)));
								echo '<pre>'.$mc.'</pre>';
								$dt = $this->book($mc);

								if (isset($dt['isbn']['isbn13']))
								{
									$isbn = $dt['isbn']['isbn13'];
								}
								$tipo = $rdf->find_class('Book');
								$status = 1;

								$place = 1;		
								echo '===>'.$place;						
								$rst = $this->books_item->tombo_insert($tombo, $isbn, $tipo, $status, $place);
								$sx .= '<li>'.$tombo.' - '.$rst[1].'</li>';
								exit;
							}
					}

				return($sx);
		}

	function marc_export($id)
		{
            $rdf = new rdf;
            $rdf->base = 'propel.';

            $dt = $rdf->le($id);

            $dtd = $rdf->le_data($id);
            echo '<pre>';
            $marc = '';
            $expr = array();
            $mani = array();
            $item = array();

            for ($r=0;$r < count($dtd);$r++)
                {
                    $ln = $dtd[$r];
                    $class = $ln['c_class'];                    
                    switch($class)
                        {
                            case 'hasAuthor':
                                $marc .= '100 ## $a '.nbr_author($ln['n_name'],7).cr();
                                break;
                            case 'hasTitle':
                                $marc .= '245 ## $a '.$ln['n_name'].cr();
                                break;
                            case 'hasDateFirstWork':
                                $marc .= '260 ## $c '.$ln['n_name'].cr();
                                break;
                            case 'isRealizedThrough':
                                array_push($expr,$ln['d_r2']);
                                break;
                        }
                }

            /* Expressão */
            for ($r=0;$r < count($expr);$r++)
                {
                    $dte = $rdf->le_data($expr[$r]);                    
                    for ($y=0;$y < count($dte);$y++)
                        {
                            $ln = $dte[$y];
                            $class = $ln['c_class'];                    
                            switch($class)
                                {                            
                                    case 'isEmbodiedIn':
                                    $idm = $ln['d_r2'];
                                    array_push($mani,$idm);
                                    break;

                                    case 'hasLanguageExpression':
                                    $marc .= '245 ## $l '.$ln['n_name'].cr();
                                    break;

                                    case 'hasFormExpression':
                                    $marc .= '245 ## $t '.$ln['n_name'].cr();
                                    break;
                                }
                        }
                }

             /* Manifestacao - isEmbodiedIn */
            for ($r=0;$r < count($mani);$r++)
                {
                    $dte = $rdf->le_data($mani[$r]);

                    for ($y=0;$y < count($dte);$y++)
                        {
                            $ln = $dte[$y];
                            $class = $ln['c_class'];
                            switch($class)
                                {
                                    case 'hasColorclassification':
                                    $marc .= '085 ## $a '.$ln['n_name'].cr();
                                    break;

                                    case 'hasISBN':
                                    $marc .= '022 ## $a '.$ln['n_name'].cr();
                                    break;

                                    case 'dateOfPublication':
                                    $marc .= '260 ## $c '.$ln['n_name'].cr();
                                    break;

                                    case 'isPlaceOfPublication':
                                    $marc .= '260 ## $b '.$ln['n_name'].cr();
                                    break;

                                    case 'isPublisher':
                                    $marc .= '260 ## $a '.$ln['n_name'].cr();
                                    break;

                                    case 'hasPage':
                                    $marc .= '300 ## $a '.$ln['n_name'].cr();
                                    break;


                                    case 'hasSubject':
                                    $marc .= '650 ## $a '.$ln['n_name'].cr();
                                    break;

                                    case 'hasCover':
                                    array_push($item,$ln['n_name']);
                                    $marc .= '952 ## $a '.$ln['n_name'].cr();
                                    break;                                    

                                    case 'isExemplifiedBy':
                                    array_push($item,$ln['n_name']);
                                    $marc .= '951 ## $a '.$ln['n_name'].cr();
                                    break;
                                }
                        }
                }
            return($marc);			
		}

	function book($t)
	{
		$type = "MARC2";
		if (strlen($t)==13)
		{
			$isbn = $t;
			$file = $this->isbn->file_locate($isbn,$type);
			if (file_exists($file))
			{
				$size = filesize($file);
				if ($size > 0)
				{
					$t = file_get_contents($file);
				} else {
					return("OPS - erro de arquivo");
				}
			}		
		}
		$title = '';
		$t = troca($t,';','.,');
		$t = troca($t,chr(13),';');
		$t = troca($t,chr(10),';');
		$ln = splitx(';',$t);
		$w = array();
		$w['title'] = '';
		$w['authors'] = array();
		$w['agents'] = array();
		$w['cover'] = '';
		$w['subject']= array();
		$w['descricao'] = '';
		$w['editora'] = '';
		$w['pages'] = '';
		$w['isbn'] = array();
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
				
				case '110':
					$sr = $this->extract($l,'a');
					$sr = nbr_author($sr,8);
					array_push($w['agents'],$sr);
				break;				
				
				case '710':
					$sr = $this->extract($l,'a');
					$sr = nbr_author($sr,8);
					array_push($w['agents'],$sr);
				break;

				/* CDD */
				case '085':
					$sr = $this->extract($l,'a');
					if (!isset($w['cdd_cor']))
						{
							$w['cdd_cor'] = array();
						}
					$w['cdd_cor'][$sr] = 1;
					
				break;								
				
				/* CDD */
				case '082':
					$sr = $this->extract($l,'a');
					if (!isset($w['cdu']))
						{
							$w['cdu'] = array();
						}
					$w['cdu'][$sr] = 1;
				break;			
				
				/* CDU */
				case '080':
					$sr = $this->extract($l,'a');
					if (!isset($w['cdd']))
						{
							$w['cdd'] = array();
						}
					$w['cdd'][$sr] = 1;
				break;

				/* CDU */
				case '952':
					$sr = $this->extract($l,'a');
					if (strlen($sr) > 0)
					{
						$w['cover'] = '_repositorio/Image/'.trim($sr);
					}
				break;							
				
				/***************** Título ***********/
				case '245':
					$sr = $this->extract($l,'a');
					$sb = $this->extract($l,'b');
					if (strlen($sb) > 0)
					{ $sr = trim($sr).': '.trim($sb); }
					$title = $sr;
					if (strlen($sr) > 0)
						{
							$w['title'] = $sr;
						}
					
				break;
				
				/***************** Editora ***********/
				case '260':
					$sr = $this->extract($l,'b');
					if (strlen($sr) > 0)
					{
					$w['editora'] = $sr;
					}
					
					$sr = $this->extract($l,'a');
					if (strlen($sr) > 0)
					{
					$w['place'] = $sr;				
					}
					
					$sr = $this->extract($l,'c');
					if (strlen($sr) > 0)
					{
					$w['data'] = $sr;
					}					
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
					if (strlen($sr) > 0)
					{
						$w['volume'] = 'v. '.$sr;
					}
					
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
	
	if (strlen($w['title']) > 0)
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

function save_marc($isbn,$txt)
{
	$type = 'MARC2';
	$file = $this->isbn->file_locate($isbn,$type);
	file_put_contents($file, $txt);
	return(1);
}

function form()
{
	$sx = '';
	
	$sx .= '<div id="marc_form">';
	$sx .= '<form method="post">';
	$sx .= msg('marc_insert_text');
	
	$sx .= '<textarea name="marc21" class="form-control form_textarea" rows=15 >'.get("marc21")."</textarea>'";
	$sx .= '<input type="submit" class="btn btn-outline-primary" value="Importar MARC21">';
	$sx .= '</form>';
	$sx .= '</div>';
	
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
}
