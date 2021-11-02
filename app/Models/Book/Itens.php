<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Itens extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'find_item';
	protected $primaryKey           = 'id_i';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_l','i_tombo','i_manifestation',
		'i_titulo','i_status'
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

	function le($id)
		{
			$dt = $this->Find($id);
			return $dt;
		}

function showItens($dt)
	{
		$Covers = new \App\Models\Book\Covers();

		$dt = $dt['data'];
		$wh = array();

		for ($r=0;$r < count($dt);$r++)
			{
				$line = $dt[$r];
				$class = $line['c_class'];
				
				switch($class)
					{
						case 'isPublisher':
							array_push($wh,$line['d_r1']);
							break;
						default:
							echo '<br>===>'.$class;
							break;
					}
			}
			$sx = '';
			for ($r=0;$r < count($wh);$r++)
				{
					$sx .= bsc('<a href="'.PATH.'v/'.$wh[$r].'">'.$Covers->get_Nail($wh[$r]).'</a>',2);
				}
			$sx = bs($sx);
			return $sx;
	}
function showCoverItem($id)
	{
			$RDFExport = new \App\Models\RDF\RDFExport();
			$RDFExport->exportNail($id);
		exit;
	}
function recomendations($m)
			{
				$Cover = new \App\Models\Book\Covers();
				$tela = '';
				$offset = round(date("s"));
				$limit = 6;
				$Item = new \App\Models\Book\Itens();
				$sql = "select i_manitestation,i_titulo,i_identifier";
				$sql .= " from ".$this->table." ";
				$sql .= "where i_library = ".LIBRARY." ";
				$sql .= "group by i_manitestation,i_titulo,i_identifier ";
				$sql .= "LIMIT $limit OFFSET $offset";

				$dt = $Item->query($sql)->getResult();

				$tela .= '<div class="row">';
				$tela .= '<div class="find.recomendations supersmall">'.lang('find.recomendations').'</div>';
				for ($r=0;$r < count($dt);$r++)
					{
						$line = (array)$dt[$r];
						$mani = $line['i_manitestation'];
						if ($mani > 0)
						{
							$isbn = $line['i_identifier'];
							$cover = $Cover->get_cover($isbn);
							$img = '<img src="'.$cover.'" class="img-fluid shadow-lg p-1 mb-2 bg-body rounded">';	
							$tela .= '<a href="'.PATH.'v/'.$mani.'">';
							$tela .= $img;
							$tela .= '</a>';
						}
					}
				$tela .= '</div>';
				return $tela;
			}
}
