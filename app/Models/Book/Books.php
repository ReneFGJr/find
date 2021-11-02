<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Books extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'books';
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

	var $dir_images = '../_covers/image/';


	function latest_acquisitions()
		{
			$url = 'https://ufrgs.br/find/_covers/image/';
			$url = 'http://find/_covers/image/';
			$sx = '';
			$Find_Item = new \App\Models\Find_Item();
			$Cover = new \App\Models\Book\Covers();

			$Find_Item->select('i_library, id_i, i_titulo, i_identifier,i_manitestation');
			//$Find_Item->select('*');
			$Find_Item->distinct();
			$Find_Item->where('i_library',LIBRARY);
			$Find_Item->orderBy('id_i DESC');
			$Find_Item->limit(20);
			$result = $Find_Item->Find();

			for($r=0;$r < count($result);$r++)
				{
					$line = $result[$r];
					$img = $Cover->get_cover($line['i_identifier']);
					$sx .= $this->card($line,$img);
				}
			return bs($sx);
		}

	function color($dt)
	{
		$cor1 = '#000000';
		$cor2 = '#FFFFFF';

		$n = $dt['n_name2'];
		if (strpos($n,'#'))
			{
				$nc = substr($n,strpos($n,'#')+1,strlen($n));
				$n = substr($n,0,strpos($n,'#'));
				if (strpos($nc,'#'))
					{
						$cor2 = substr($nc,strpos($nc,'#'),strlen($nc));
						$cor1 = '#'.substr($nc,0,strpos($nc,'#'));
					} else {
						$cor1 = '#'.$nc;
					}
			}

		$tela = '
			<div style="width: 100%; padding: 3px; background-color: '.$cor1.';">
			<a href="'.PATH.'v/'.$dt['d_r2'].'" 
			style="color: '.$cor2.'; padding: 2px 5px;">
			'.$n.'
			</a></div>';
		return $tela;
	}

	function link($dt)
		{
			$n = $dt['n_name2'];
			$tela = '
			<a href="'.PATH.'v/'.$dt['d_r2'].'">'.$n.'</a>';
			return $tela;
		}

	function work($w)
		{
			$title = $w['concept']['n_name'];

			$dt = $w['data'];
			$trad = '';
			$auth = '';
			$ilus = '';
			$org = '';
			$pre_auth = '';
			$pre_trad = '';
			$pre_ilus = '';
			$pre_org = '';

			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$class = $line['c_class'];
					switch($class)
					{
					case 'hasIllustrator':
						if (strlen($ilus) > 0) { $pre_ilus .= 'es'; }
						$ilus .= $this->link($line).' ';
						break;
					case 'hasAuthor':
						if (strlen($auth) > 0) { $pre_auth .= 'es'; }
						$auth .= $this->link($line).' ';
						break;	
					case 'hasOrganizator':
						if (strlen($org) > 0) { $pre_org .= 'es'; }
						$org .= $this->link($line).' ';
						break;							

					case 'hasTranslator':
						if (strlen($trad) > 0) { $pre_trad .= 'es'; }
						$trad .= $this->link($line).' ';;
						break;
					case 'isAppellationOfExpression':
						/* none */
						break;
					case 'prefLabel':
						/* none */
						break;						
					default:
						echo '=======work=>'.$class.'<br>';
					}
				}

			$tela = '<div class="find.title supersmall">'.h($title,2).'</div>';
			$tela .= '<div class="authors mb-4">';

			if (strlen($org) > 0)
				{
					$tela .= '<div class="find.author">'.lang('find.organizator'.$pre_org).': '.$org.'(Orgs)</div>';
				}

			if (strlen($auth) > 0)
				{
					$tela .= '<div class="find.author">'.lang('find.author'.$pre_auth).': '.$auth.'</div>';
				}	

			if (strlen($ilus) > 0)
				{
					$tela .= '<div class="find.author">'.lang('find.ilustrator'.$pre_ilus).': '.$ilus.'</div>';
				}

			if (strlen($trad) > 0)
				{
					$tela .= '<div class="find.translater">'.lang('find.translater'.$pre_trad).': '.$trad.'</div>';;
				}
			$tela .= '</div>';
			return $tela;
		}	

	function manifestation($m)
		{
			$dt = $m['data'];
			$d['ano'] = array();
			$classification = '';
			$classificationf = '';
			$year = '';
			$page = '';
			$subject = '';
			$vol = '';
			$place = '';
			$editora = '';
			$description = '';
			$cutter = '';
			$tela = '';
			for ($r=0;$r < count($dt);$r++)
				{
					$line = $dt[$r];
					$prefix = $line['prefix_ref'];
					$class = $line['c_class'];
					$dr1 = $line['d_r1'];
					$dr2 = $line['d_r2'];
					$dr3 = $line['n_name2'];
					$dli = $line['d_literal'];
					$lib = $line['d_library'];

					$class2 = $class;
					if ((substr($class,3,14) == 'Classification') or (substr($class,3,13) == 'Classificacao'))
						{
							$class2 = 'Classification';
						}

					switch($class2)
						{
							case 'hasCutter':
								$cutter .= '<div class="find.cutter">'.$this->link($line).'</div>';
							break;

							case 'Classification':
								$classificationf .= '<div>'.lang('find.'.$class).': '.$this->link($line).'</div>';
							break;

							case 'hasColorclassification':
								$classification .= $this->color($line);
							break;

							case 'dateOfPublication':
								$year .= $this->link($line);
								break;

							case 'hasPage':
								$page = $this->link($line);
								break;

							case 'hasSubject':
								$subject .= $this->link($line).'. ';
								break;

							case 'hasVolumeNumber':
								$vol = $this->link($line);
								break;

							case 'isPlaceOfPublication':
								$place = $this->link($line);
								break;

							case 'isPublisher':
								$editora = $this->link($line);
								break;

							case 'description':
								$description = '<p><b>'.lang('find.description').'</b>: '.$line['n_name'].'</p>';
								break;

							case 'isAppellationOfExpression':
								/* none */
								break;

							case 'prefLabel':
								/* none */
								break;

							case 'isAppellationOfManifestation':
								/* none */
								break;

							default:
								echo '====>'.$prefix.':'.$class.'--'.$dr1.'-'.$dr2.'-'.$dr3.'<br>';
								echo '<hr>';							
								break;
						}
				}

				if (strlen($classification.$classificationf.$cutter) > 0)
					{
						$tela .= '<div class="row mb-4">';
						$tela .= '<div class="col-md-2 find.classification_label">'.lang('find.classification').':</div>';
						$tela .= '<div class="col-md-10 find.classification_class">';
						$tela .= $classification;
						$tela .= $classificationf;
						if ($cutter != '') 
							{ 
								$tela .= $cutter;
							}
						$tela .= '</div>';
						$tela .= '</div>';
					}

				if (strlen($year) > 0)
					{
						$tela .= '<div>'.lang('find.year').': '.$year.'</div>';
					}

				if (strlen($editora) > 0)
					{
						$tela .= '<div>'.lang('find.publisher').': '.$editora.'</div>';
					}

				if (strlen($place) > 0)
					{
						$tela .= '<div>'.lang('find.place').': '.$place.'</div>';
					}					


				if (strlen($page) > 0)
					{
						$tela .= '<div>'.lang('find.total_pages').': '.$page.'</div>';
					}

				if (strlen($subject) > 0)
					{
						$tela .= '<div>'.lang('find.subject').': '.$subject.'</div>';
					}	
				if (strlen($description) > 0)
					{
						$tela .= '<div class="mt-5">'.$description.'</div>';
					}				
				
				if (strlen(trim($vol)) > 0)
					{
						$tela .= '<div>'.lang('find.volume').': '.$vol.'</div>';
					}					

				return $tela;
		}



	function viewItem2($d)
		{
			$Item = new \App\Models\Book\Itens();
			$tela = $this->view_manifestation($d);
			return $tela;
		}

	function v($id)
		{
			$RDF = new \App\Models\RDF\RDF();
			$dt = $RDF->le($id);

			$sx = '';
			$RDF = new \App\Models\Rdf\RDF();
			$RDFData = new \App\Models\Rdf\RDFData();
			$class = $dt['concept']['prefix_ref'].':'.$dt['concept']['c_class'];

			if ($class == ':Expression') { $class = 'frbr:Expression'; }
			switch($class)
				{
					case 'brapci:Editora':
					$Publisher = new \App\Models\Book\Publisher();
						$sx = $Publisher->viewManifestations($dt);
					break;

					case 'frbr:Work':
						$sx = $this->viewWork($dt);
						$sx .= $RDFData->view_data($dt);						
					break;

					case 'frbr:Manifestation':
						$sx = 'MANIFESTATION';						
						$expre = $RDF->recover($dt,'isAppellationOfManifestation');
						$expre = $expre[0];
						$expre = $RDF->le($expre);

						$work = $RDF->recover($expre,'isAppellationOfExpression');
						$work = $work[0];
						$work = $RDF->le($work);
						
						$d = array();
						$d['work'] = $work;
						$d['expression'] = $expre;
						$d['manifestattion'] = $dt;

						$sx = $this->viewItem($d);
					break;

					case 'frbr:Expression':
						$w = $RDF->recover($dt,'isAppellationOfExpression');
						$m = $RDF->recover($dt,'hasFormExpression');
						
					break;					

					default:
					$sx = $this->viewWork($dt);
					$sx .= $RDFData->view_data($dt);
				}
			return $sx;
		}

	function viewWork($dt)
		{
			$sx = '';
			$class = $dt['concept']['prefix_ref'].':'.$dt['concept']['c_class'];
			$sx .= '<h1>'.$class.'</h1>';

			$sx = bs($sx);
			return $sx;
		}				

	function card($dt,$img='')
		{
			$title = $dt['i_titulo'];
			$st = '<a href="'.PATH.'v/'.$dt['i_manitestation'].'">';
			$st .= '<img class="card-img-top" src="'.$img.'" alt="Card image cap">';
			$st .= '</a>';

			$sx = bsc(				 
						bscard($st,
									'<span class="book_title">'.$title.'</span>',
					)
				,3);			
			return $sx;
		}

	function isbn($m)
		{
			$isbn = $m['concept']['n_name'];
			$isbn = substr($isbn,5,13);
			return $isbn;
		}

	function viewItem($d)
		{
			$tela1 = '';
			$tela2 = '';
			$tela3 = '';

			$Cover = new \App\Models\Book\Covers();
			$Items = new \App\Models\Book\Itens();

			$w = $d['work'];
			$e = $d['expression'];
			$m = $d['manifestattion'];

			$isbn = $this->isbn($m);
			$cover = '<img src="'.$Cover->get_cover($isbn).'" class="img-fluid shadow-lg mb-5 bg-body rounded">';			

//			$tela1 = h($dt['i_titulo'],3);
			$tela1 .= bsc($cover,2);

			/* Recomendações */
			$tela3 .= $Items->recomendations($m);

			$tela2 .= $this->work($w);
			$tela2 .= $this->manifestation($m);
			$tela2 = bsc($tela2,9);

			$tela3 = bsc($tela3,1);


			$tela = bs($tela1.$tela2.$tela3);

			/*
			echo $mani;
			echo '<pre>';
			print_r($dt);
			print_r($manif);
			echo '</pre>';
			*/
			return $tela;
		}	

}
