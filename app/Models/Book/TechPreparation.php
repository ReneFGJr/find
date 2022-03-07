<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class TechPreparation extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'techpreparations';
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

	function index($d1,$d2,$d3,$d4)
		{
			$sx = '';
			if ($d1=='prepare_0') { $d1 = 'prepare'; $st = '0'; }
			if ($d1=='prepare_1') { $d1 = 'prepare'; $st = '1';  }
			if ($d1=='prepare_2') { $d1 = 'prepare'; $st = '2';  }
			switch($d1)
				{
					case 'item_status':
						$Itens = new \App\Models\Book\Itens();
						$Itens->status($d2,$d3);
						$link = PATH.MODULE.'tech/prepare_'.$d3.'/'.$d2;
						$sx = metarefresh($link,0);
						break;
					case 'prepare':
						/* Novos Itens */
						$Itens = new \App\Models\Book\Itens();
						$sx .= bsc($this->image_left(2),2);
						if ($d2=='')
							{
								$sx .= bsc($Itens->prepare($d2,$d3,$st),10);
							} else {
								switch($st)
									{
										case '0':
										/**************************************** HARVESTING */
											$harvesting = $Itens->harvesting_metadata($d2,$d3,$d4);
											$sa = $Itens->process_metadata($harvesting,$d2,$d3);
											$sa .= $Itens->header($d2);
											
											/**************************** Status */
											$sa .= $Itens->actions($d2,$st);

											$sx .= bsc($sa,10);
											break;

										case '1':
										/******************************************* CATALOG */
											$IDM = $Itens->create_rdf_work($d2);
											$Books = new \App\Models\Book\Books();
											$sx .= bsc($Books->a($IDM),10);
											break;
										default:
											$sx .= bsc(bsmessage('Tech '.$st.' not implemented'),10);
											break;

									}
							}
						
						$sx = bs($sx);
						break;						
						break;
					case 'prepare_I':
						/* Novos Itens */
						$Itens = new \App\Models\Book\Itens();
						$sx .= bsc($this->image_left(1),2);
						$sx .= bsc($Itens->new($d2,$d3,$d4),10);
						$sx = bs($sx);
						break;
																		
					default:
						$sx = $this->resume();
						break;
				}
			return $sx;
		}

	function image_left($tp=0)
		{
			$tp = URL.'img/lib/techpreparation.png';
			$sx = '<img src="'.$tp.'" width="100%" class="img-fluid">';
			return($sx);
		}

	function resume()
		{
			$Itens = new \App\Models\Book\Itens();
			$dt = $Itens->resume();

			$sx = '';
			$sx .= bsc($this->image_left(0),2);

			/****************************************** ITEMS */
			$sa = h(msg('find.techpreparation'),2,'mb-4');
			$st = array('I',0,1,2,3,4,9);
			$sa .= '<ul>';
			
			for ($r=0;$r < count($st);$r++)
				{
					$sa .= '<li class="h5 mb-4">';
					$sa .= '<a href="'.PATH.MODULE.'tech/prepare_'.$st[$r].'" style="border-bottom: 2px solid #AAA; width: 100%;">';
					$sa .= lang('find.tech_'.$st[$r]);
					$sa .= '</a>';

					$tot = 0;
					for ($q=0;$q < count($dt);$q++)
						{
							if ($dt[$q]['i_status'] == $st[$r])
								{
									$tot = $dt[$q]['total'];
								}
						}
					if ($tot > 0)
						{
							$sa .= '<sup class="pe-2 ps-2">'.$tot.' '.lang('find.itens').'</sup>';
						}
					
					$sa .= '</li>';
				}
			$sa .= '</ul>';
			$sx .= bsc('',1);
			$sx .= bsc($sa,9);
			$sx = bs($sx);
			return $sx;
		}
}
