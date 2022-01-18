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
			$RDF = new \App\Models\RDF\RDF();
			$Covers = new \App\Models\Book\Covers();
			$dt = $RDF->le($id);
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

	function export($id)
		{
			$tela = '';
			$RDF = new \App\Models\RDF\RDF();
			$dir = $RDF->directory($id);

			$dt = $RDF->le($id,0);
			$prefix = $dt['concept']['prefix_ref'];
			$class = $prefix.':'.$dt['concept']['c_class'];

			switch($class)
				{
					case 'dc:Issue':
						$vol = $RDF->recovery($dt['data'],'hasPublicationVolume');
						$nr = $RDF->recovery($dt['data'],'hasPublicationNumber');
						$year = $RDF->recovery($dt['data'],'dateOfPublication');

						if (isset($vol[0][1])) { $vol = $RDF->content($vol[0][1]); } else { $vol = ''; }
						if (isset($nr[0][1])) { $nr = $RDF->content($nr[0][1]); } else { $nr = ''; }
						if (isset($year[0][1])) { $year = $RDF->content($year[0][1]); } else { $year = ''; }
						
						$issue = '';
						if (strlen($nr) > 0) { $issue .= ', '.$nr; }
						if (strlen($vol) > 0) { $issue .= ', '.$vol; }
						if (strlen($year) > 0) { $issue .= ', '.$year; }
						$issue .= '.';
						$this->saveRDF($id,$issue,'name.nm');
						break;
					case 'dc:Journal':
						$name = $dt['concept']['n_name'];
						$name = '<a href="'.base_url(URL.'v/'.$id).'" class="journal">'.$name.'</a>';
						$this->saveRDF($id,$name,'name.nm');
						break;
					case 'brapci:Article':
						$tela .= 'ARTICLE';	
						/************************************************** Authors */
						$authors = $RDF->recovery($dt['data'],'hasAuthor');
						$auths = '';
						for ($r=0;$r < count($authors);$r++)
							{
								$idr = $authors[$r][1];
								if (strlen($auths) > 0) { $auths .= '; '; }
								$auths .= $RDF->content($idr);
							}
						/************************************************** Authors */
						$publisher = $RDF->recovery($dt['data'],'isPubishIn');
						$publisher = $RDF->content($publisher[0][1]);
						/************************************************** Authors */
						//$publisher = $RDF->recovery($dt['data'],'hasSource');
						

						/***************************************************** Title */
						$title = $RDF->recovery($dt['data'],'hasTitle');
						$title = nbr_title($title[0][2]);

						/***************************************************** Title */
						$issue = $RDF->recovery($dt['data'],'hasIssueOf');
						$issue = $RDF->content($issue[0][0]);
						
						/************************************************** Section */
						//$section = $RDF->recovery($dt['data'],'hasSectionOf');						

						/****************************************************** SAVE */
						$name = strip_tags($auths.'. '.$title.'. $b$'.$publisher. '$/b$'.$issue);
						$name = '<a href="'.base_url(URL.'v/'.$id).'" class="article">'.$name.'</a>';
						$name = troca($name,'$b$','<b>');
						$name = troca($name,'$/b$','</b>');
						$this->saveRDF($id,$name,'name.nm');
					break;

					case 'foaf:Person':
						$tela .= 'ARTICLE';	
						$name = nbr_author($dt['concept']['n_name'],1);
						$name = '<a href="'.base_url(URL.'v/'.$id).'" class="author">'.$name.'</a>';
						$this->saveRDF($id,$name,'name.nm');
						break;

					default:
						$name = $RDF->recovery($dt['data'],'prefLabel');
						$name = trim($name[0][2]);
						if (strlen($name) > 0)
							{
								$this->saveRDF($id,$name,'name.nm');
								break;
							}
							echo $class.'<hr>';
						exit;
						break;
				}
			$tela .= '<a href="'.base_url(PATH.'v/'.$id).'">'.$name.'</a>';
			return $tela;
		}

		function saveRDF($id,$value,$file)
			{
				$RDF = new \App\Models\RDF\RDF();
				$dir = $RDF->directory($id);
				$file = $dir.$file;
				file_put_contents($file,$value);
				return true;
			}
}
