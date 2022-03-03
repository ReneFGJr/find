<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFExport extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = PREFIX.'rdfexports';
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

	function exportNail($id)
		{
			$this->RDF = new \App\Models\RDF\RDF();
			$Covers = new \App\Models\Book\Covers();
			$dt = $this->RDF->le($id);
			$class = $dt['concept']['c_class'];
			switch($class)
				{
					case 'Manifestation':
						$isbn = substr($dt['concept']['n_name'],5,13);
						$cover_nail = $Covers->get_cover($isbn);
						return $cover_nail;
						break;
				}
		}

	function recover_authors($dt)
		{
			/************************************************** Authors */
			$authors = $this->RDF->recovery($dt['data'],'hasAuthor');
			$auths = '';
			$auth = array();
			for ($r=0;$r < count($authors);$r++)
				{
					$idr = $authors[$r][1];
					if (strlen($auths) > 0) { $auths .= '; '; }

					$auth_name = strip_tags($this->RDF->c($idr));
					$auth_id = $idr;
					$auths .= $auth_name;
					array_push($auth,array('name'=>$auth_name,'id'=>$auth_id));
				}
			return $auth;
		}	

	function recover_title($dt)
		{
			$title = $this->RDF->recovery($dt['data'],'hasTitle');
			if (isset($title[0][2]))
				{
					$title = nbr_title($title[0][2]);
				} else {
					$title = '## FALHA NO TÍTULO ##';
				}
			return $title;
		}	

	function recover_subject($dt)
		{
			$Subject = $this->RDF->recovery($dt['data'],'hasSubject');
			return $Subject;
		}			
	function recover_issue($dt,$id,$tp)
		{
			$issue1 = $this->RDF->recovery($dt['data'],'hasIssueOf');
			$issue2 = $this->RDF->recovery($dt['data'],'hasIssueProceedingOf');
			$issue = array_merge($issue1,$issue2);

			/* EMPTY */
			if (!isset($issue[0]))  { return 'NoN'; } 

			$issues = array();
			for ($r=0;$r < count($issue);$r++)
				{
					$line = $issue[$r];
					$id1 = $line[0];
					$id2 = $line[1];
					if ($id1 == $id) 
						{
							$idx = $id2;
						} else {
							$idx = $id1;
						}
					array_push($issues,$this->RDF->c($idx));
				}
			return $issues;
		}
	function export_article($dt,$id,$tp='A')
		{
			$ABNT = new \App\Models\Metadata\Abnt();
			$publisher = '';
			$tela = 'Export Article';	
			/*************************************************** Authors */
			$auths = $this->recover_authors($dt);
			$auths_text = '';
			for($r=0;$r < count($auths);$r++)
				{
					if ($auths_text != '') { $auths_text .= '; '; }
					$auths_text .= $auths[$r]['name'];
				}			
			$dta['author'] = $auths;

			/***************************************************** Title */
			$title = $this->recover_title($dt);
			$title = $this->RDF->string($title);
			$dta['title'] = $title;

			/***************************************************** Issue */
			$issue = $this->recover_issue($dt,$id,$tp);
			$issue = $this->RDF->string($issue,1);

			/************************************************ Proceeding */
			if ($tp == 'P') 
				{ 
					$issue = '<b>Anais...</b> '.$issue;
				}

			/**************************************************** PAGE */
			$abs =  $this->RDF->recovery($dt['data'],'hasAbstract');			
			$abs = $this->RDF->string_array($abs,1);

			/**************************************************** PAGE */
			$subj =  $this->RDF->recovery($dt['data'],'hasSubject');
			$subj_text = $this->RDF->string_array($subj);
			
			/**************************************************** PAGE */
			$pagf =  $this->RDF->recovery($dt['data'],'hasPageEnd');
			$pagi =  $this->RDF->recovery($dt['data'],'hasPageStart');
			$pagf = $this->RDF->string($pagf,1);
			$pagi = $this->RDF->string($pagi,1);

			if (strlen($pagf.$pagi) > 0)
				{
					$issue = substr($issue,0,strlen($issue)-5).
								'p. $p, '.
								substr($issue,strlen($issue)-5,5);
					$p = trim($pagi);
					if (strlen($pagf) > 0)
						{
							if (strlen($p) > 0) { $p .= '-'; }
							$p .= $pagf;
						}
					$issue = troca($issue,'$p',$p);
				}

			/************************************************** Section */
			//$section = $this->RDF->recovery($dt['data'],'hasSectionOf');						

			/****************************************************** MOUNT */
			$publisher = '';

			/* Formata para Artigo ABNT */
			if ($tp == 'A')
				{
					$issue = '<b>'.
								substr($issue,0,strpos($issue,',')).
							'</b>'.
								substr($issue,strpos($issue,','),strlen($issue));
				}
			$name = strip_tags($auths_text.'. ');
			$name .= '<a href="'.(URL.'v/'.$id).'" class="article">'.$title.'</a>';
			$name .= '. $b$'.$publisher. '$/b$'.$issue;
			$name = troca($name,'$b$','<b>');
			$name = troca($name,'$/b$','</b>');
			$this->saveRDF($id,$name,'name.nm');

			/******************************** Authors */
			$autores = json_encode($dta['author']);
			$this->saveRDF($id,$autores,'authors.json');			

			/******************************** Subject */
			$subj = explode(';',$subj_text);
			$subjects = json_encode($subj);
			$this->saveRDF($id,$subjects,'keywords.json');

			/********************************* YEAR */
			$year = sonumero($issue);
			$year = round(substr($year,strlen($year)-4,4));
			if (($year > 1950) and ($year <= (date("Y")+1)))
			{
				$this->saveRDF($id,$year,'year.nm');
			}			

			return '';
		}

	function export_person($dt,$id)
		{
				$sx = '';	
				$name = $dt['concept']['n_name'];
				$name = nbr_author($name,1);
				$name = '<a href="'.(URL.'v/'.$id).'" class="author">'.$name.'</a>';
				$this->saveRDF($id,$name,'name.nm');
				return $sx;
		}

	function export_journal($dt,$id)
		{
				$sx = 'JOURNAL';	
				$name = $dt['concept']['n_name'];
				$name = nbr_author($name,7);
				$name = '<a href="'.(URL.'v/'.$id).'" class="author">'.$name.'</a>';
				$this->saveRDF($id,$name,'name.nm');
				return $sx;
		}

	function export_issueproceedings($dt,$id)
		{
			$name = $dt['concept']['n_name'];
			$year = sonumero($name);
			$year = substr($year,strlen($year)-4,4);

			$this->saveRDF($id,$name,'name.nm');
			$this->saveRDF($id,$year,'year.nm');
			return "";
		}				

	function export_issue($dt,$id)
		{

		/******************************** ISSUE */
			$class = $dt['concept']['c_class'];
			$vol = $this->RDF->recovery($dt['data'],'hasPublicationVolume');
			$nr = $this->RDF->recovery($dt['data'],'hasPublicationNumber');
			$year1 = $this->RDF->recovery($dt['data'],'dateOfPublication');						
			$year2 = $this->RDF->recovery($dt['data'],'hasDateTime');
			$year = array_merge($year1,$year2);
			$place = $this->RDF->recovery($dt['data'],'hasPlace');
			$publish = $this->RDF->recovery($dt['data'],'hasIssue');

			$dc['id'] = $id;

			/****************************************************** PUBLISH **/
			$namePublish = strip_tags($this->RDF->c($publish[0][1]));
			$dc['publish'] = array('id'=>$publish[0][1],
						'name'=>$namePublish);						
		
			/********************************************************** YEAR */
			if (isset($year[0][1]))
				{			
					$nameYear = $this->RDF->c($year[0][1]);
					$dc['year'] = array('id'=>$year[0][1],
						'name'=>$nameYear);
				} else {
					if ($dt['concept']['n_name'] == 'ISSUE:')
						{
							$RDFErros = new \App\Models\Rdf\RdfErros();
							$RDFErros->append(1,'RDF ISSUE ERROR - SEM ANO',$id);
							$name = $namePublish.'==ERRO=='.$id;
							$this->saveRDF($id,$name,'name.nm');
							$this->saveRDF($id,0,'year.nm');
							return "";
						}
				}

			/********************************************************** VOL. */
			if (isset($vol[0][1]))
				{			
					$nameVol = $this->RDF->c($vol[0][1]);
					$dc['vol'] = array('id'=>$vol[0][1],
						'name'=>$nameVol);
				} else {
					$nameVol = '';
				}

			/********************************************************** NR. **/
			if (isset($nr[0][1]))
			{			
			$nameNr = $this->RDF->c($nr[0][1]);
			$dc['nr'] = array('id'=>$nr[0][1],
						'name'=>$nameNr);						
			} else {
				$nameNr = '';
			}
			/******************************************************** PLACE **/
			if (isset($place[0][1]))
				{
					$namePlace = $this->RDF->c($place[0][1]);
					$dc['place'] = array('id'=>$place[0][1],
							'name'=>$namePlace);						
				} else {
					$dc['place'] = array();	
					$namePlace = '';					
				}

			$issue = $namePublish;
			if (strlen($nameNr) > 0) { $issue .= ', '.$nameNr; }
			if (strlen($namePlace) > 0) { $issue .= ', '.$namePlace; }
			if (strlen($nameVol) > 0) { $issue .= ', '.$nameVol; }
			if (strlen($nameYear) > 0) { $issue .= ', '.$nameYear; }
			$issue .= '.';

			$dc['abnt'] = $issue;

			/**************************************************** Vancouver */
			$issue_vancouver = $namePublish;
			if (strlen($nameYear) > 0) { $issue_vancouver .= '. '.$nameYear; }
			//if (strlen($namePlace) > 0) { $issue_vancouver .= ', '.$namePlace; }
			if (strlen($nameVol) > 0) { $issue_vancouver .= ' '.sonumero($nameVol); }
			if (strlen($nameNr) > 0) { $issue_vancouver .= '('.sonumero($nameNr).')'; }		
			$issue_vancouver .= '.';

			$dc['vancouver'] = $issue_vancouver;

			$name = $issue;
			$this->saveRDF($id,$name,'name.nm');
			$this->saveRDF($id,$issue,'name.abnt');
			$this->saveRDF($id,$issue_vancouver,'name.vancouver');
			$this->saveRDF($id,$nameYear,'year.nm');
			$this->saveRDF($id,json_encode($dc),'issue.json');
			return "";
		}
	function export_geral($dt,$id)
		{
			$name = trim($dt['concept']['n_name']);

			if (!isset($dt['concept']['id_cc'])) { return 'NoN'; }
						
			if (strlen($name) == 0)
				{
					$name = $this->RDF->recovery($dt['data'],'prefLabel');
					$name = trim($name[0][2]);
				}		
			if (strlen($name) > 0)
				{
					$this->saveRDF($id,$name,'name.nm');
				}
			return '';			
		}

	function export($id,$FORCE=false)
		{
			$tela = '';
			$this->RDF = new \App\Models\RDF\RDF();
			$dir = $this->RDF->directory($id);
			$file = $dir.'name.nm';
			if ((file_exists($file)) and ($FORCE == false))
				{
					return '';
				}

			$dt = $this->RDF->le($id,0);
			$prefix = $dt['concept']['prefix_ref'];
			$class = $prefix.':'.$dt['concept']['c_class'];
			$name = ':::: ?'.$class.'? ::::';
			//echo '<br>'.$name.'-->'.$id;;
			
			switch($class)
				{
					/*************************************** ARTICLE */
					case 'brapci:Article':
						$this->export_article($dt,$id,'A');
						break;

					case 'brapci:Proceeding':
						$this->export_article($dt,$id,'P');
						break;

					/*************************************** ISSUE ***/
					case 'dc:Issue':
						$this->export_issue($dt,$id);
						break;

					/*************************************** ISSUE ***/
					case 'brapci:IssueProceeding':
						$this->export_issueproceedings($dt,$id);
						break;						

					/*************************************** ISSUE ***/
					case 'foaf:Person':
						$this->export_person($dt,$id);
						break;	

					/*************************************** VOLUME */
					case 'brapci:PublicationVolume':
						$this->export_geral($dt,$id);
						break;

					/*************************************** Number */
					case 'brapci:Number':
						$this->export_geral($dt,$id);
						break;

					/*************************************** Gender */
					case 'brapci:Gender':
						$this->export_geral($dt,$id);
						break;						

					/******************************* Corporate Body */
					case 'frbr:CorporateBody':
						$this->export_geral($dt,$id);
						break;

					/************************************** SECTION */
					case 'brapci:ProceedingSection':
						$this->export_geral($dt,$id);
						break;

					/************************************** Subject */
					case 'dc:Subject':
						$this->export_geral($dt,$id);
						break;						

					/*************************************** PLACE **/
					case 'frbr:Place':
						$this->export_geral($dt,$id);
						break;	

					/*************************************** NUMBER */
					case 'brapci:PublicationNumber':
						$this->export_geral($dt,$id);
						break;

					/*************************************** Date ***/					
					case 'brapci:Date':
						$this->export_geral($dt,$id);
						break;

					case 'dc:Journal':		
						$this->export_journal($dt,$id);
						break;
					
					case 'brapci:FileStorage':
						$this->export_geral($dt,$id);
						break;

					default:
						echo '<br> Exportando ====>'.$name;
						$this->export_geral($dt,$id);
						break;
				}
			$tela .= '<a href="'.(PATH.MODULE.'v/'.$id).'">'.$name.'</a>';
			return $tela;
		}

		function saveRDF($id,$value,$file)
			{
				$this->RDF = new \App\Models\RDF\RDF();
				$dir = $this->RDF->directory($id);
				$file = $dir.$file;
				file_put_contents($file,$value);
				return true;
			}
}
