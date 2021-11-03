<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFData extends Model
{
	var $DBGroup              = 'default';
	protected $table                = 'rdf_data';
	protected $primaryKey           = 'id_d';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_d','d_r1','d_r2','d_p','d_library','d_literal'
	];

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

	function literal($id,$prop,$name)
		{
			$RDFClass = new \App\Models\RDF\RDFClass();
			$idp = $RDFClass->class($prop);

			$RDFLiteral = new \App\Models\RDF\RDFLiteral();
			$d['d_literal'] = $RDFLiteral->name($name);
			$d['d_library'] = LIBRARY;
			$d['d_r1'] = $id;
			$d['d_r2'] = 0;
			$d['d_p'] = $idp;

			$rst = $this->where('d_r1',$id)->where('d_literal',$d['d_literal'])->FindAll();
			if (count($rst) == 0)
				{
					$this->insert($d);
					$rst = $this->where('d_r1',$id)->where('d_literal',$d['d_literal'])->FindAll();
				}
			$id = $rst[0]['id_d'];
			return $id;
		}

	function check($dt)
		{
			foreach($dt as $field=>$value)
				{
					$this->where($field,$value);
					//echo '<br>'.$field.'==>'.$value;
				}
			$dts = $this->first();

			if (!is_array($dts))
				{

					$this->insert($dt);
					return true;
				}
			return false;
		}

	function view_data($dt)
		{
			$RDF = new \App\Models\RDF\RDF();
			$sx = '';
			$ID = $dt['concept']['id_cc'];
			if (isset($dt['data']))
				{
					$dtd = $dt['data'];
					for ($r=0;$r < count($dtd);$r++)
						{
							$line = (array)$dtd[$r];
							$sx .= bsc('<small>'.lang($line['prefix_ref'].':'.
									$line['c_class'].'</small>'),2,
									'supersmall border-top border-1 border-secondary my-2');
							if ($line['d_r2'] != 0)
							{
								if ($ID == $line['d_r2'])
									{
										$link = base_url(PATH.MODULE.'/v/'.$line['d_r1']);
										$txt = $RDF->info($line['d_r1'],1);
										if (strlen($txt) > 0)
											{
												$link = '<a href="'.$link.'">'.$txt.'</a>';
											} else {
												$txt = 'not found:'.$line['d_r1'];
												$link = '<a href="'.$link.'">'.$txt.'</a>';
											}
										
									} else {
										$link = base_url(PATH.MODULE.'/v/'.$line['d_r2']);
										$txt = $RDF->info($line['d_r2'],1);
										if (strlen($txt) > 0)
											{
												$link = '<a href="'.$link.'">'.$txt.'</a>';
											} else {
												$txt = 'not found:'.$line['d_r2'];
												$link = '<a href="'.$link.'">'.$txt.'</a>';
											}
									}
								$sx .= bsc($link,10,'border-top border-1 border-secondary my-2');
							} else {
								$txt = $line['n_name'];
								$lang = $line['n_lang'];
								if (strlen($lang) > 0)
									{
										$txt .= ' <sup>('.$lang.')</sup>';
									}
								if (substr($txt,0,4) == 'http')
									{
										$txt = '<a href="'.$line['n_name'].'" target="_blank">'.$txt.'</a>';
									}
								
								$sx .= bsc($txt,10,'border-top border-1 border-secondary my-2');
							}
							
						}
				}
			return bs($sx);
		}

	function le($id)
		{
			$this->join('rdf_name', 'd_literal = rdf_name.id_n', 'LEFT');
			$this->join('rdf_class', 'rdf_data.d_p = rdf_class.id_c', 'LEFT');
			$this->join('rdf_prefix', 'rdf_class.c_prefix = rdf_prefix.id_prefix', 'LEFT');

			//rderBy('rdf_class.c_class, rdf_name.n_name');
			$sql = "select ";
			$sql .= " DISTINCT 
    		rdf_name.id_n, rdf_name.n_name, rdf_name.n_lang, 
			rdf_class.c_class, rdf_class.c_prefix, rdf_class.c_type, 
			rdf_prefix.prefix_ref, rdf_prefix.prefix_url, 
    		rdf_data.*
			";

			$sql .= "from ".PREFIX."rdf_data ";
			$sql .= "left join ".PREFIX.".rdf_name ON d_literal = rdf_name.id_n ";
			$sql .= "left join ".PREFIX.".rdf_class ON rdf_data.d_p = rdf_class.id_c ";
			$sql .= "left join ".PREFIX.".rdf_prefix ON rdf_class.c_prefix = rdf_prefix.id_prefix ";
			$sql .= "where (d_r1 = $id) OR (d_r2 = $id)";
			$sql .= "order by c_class, d_r1, d_r2, n_name";
			$dt = (array)$this->db->query($sql)->getResult();			
			return($dt);
		}	
}

