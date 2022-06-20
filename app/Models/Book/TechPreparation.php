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

	function banner($fs='')
		{
			switch($fs)
				{
					case 'prepare_0':
						$tit = h('Catalogação de um Item');
						break;					
					case 'prepare_I':
						$tit = h('Incluir no acervo');
						break;
					default:
						$tit = h('Preparo técnico');
				}
			$sx = '';
			$sx = bsc('<img src="'.URL.'img/task/cataloging.png" class="img-fluid">',2,'bg-secondary');
			$sx .= bsc($tit,10,'bg-secondary text-center p-4 text-light');
			$sx .= bsc($fs.'&nbsp;',12,'mb-3');
			$par = array('fluid' => true);
			$sx = bs($sx,$par);

			return $sx;
		}

	function index($d1,$d2,$d3,$d4)
		{
			$Cover = new \App\Models\Book\Covers();
			$Style = new \App\Models\Style\MenuBtn();
			if (!perfil("#CAT#ADM"))
				{
					echo metarefresh(PATH.MODULE);
					exit;
				}
			
			$sx = '';
			$sx .= $this->banner($d1);
			if ($d1=='prepare_0') { $d1 = 'prepare'; $st = '0'; }
			if ($d1=='prepare_1') { $d1 = 'prepare'; $st = '1';  }
			if ($d1=='prepare_2') { $d1 = 'prepare'; $st = '2';  }

			switch($d1)
				{
					case 'item_status':
						$Itens = new \App\Models\Library\Itens();
						$Itens->status($d2,$d3);
						$link = PATH.MODULE.'tech/prepare_'.$d3.'/'.$d2;
						$sx .= metarefresh($link,0);
						break;
					case 'prepare':
						/* Novos Itens */
						$Itens = new \App\Models\Library\Itens();
						$dt = $Itens->find($d2);						
						//$sz = bsc($this->image_left(2),2);
						$img = '<img id="cover" src="'.$Cover->get_cover($dt['i_identifier']).'" class="img-fluid">';
						$sz = bsc($img,2);
						if ($d2=='')
							{
								$sz .= bsc($Itens->prepare($d2,$d3,$st),10);
							} else {
								switch($st)
									{
										case '0':
										/**************************************** HARVESTING */
											$sa = '';
											$harvesting = $Itens->harvesting_metadata($d2,$d3,$d4);
											/* Processamento */
											$sa .= $Itens->process_metadata($harvesting,$d2,$d3);
																					
											/**************************** Status */
											$dt = $Itens->find($d2);
											$sa .= ''.$Itens->actions($d2,$st);	

											$itz = array();
											switch($dt['i_status'])
											{
												case '0':
													$itz['find.tech_1'] = PATH.MODULE.'tech/item_status/'.$d2.'/1';
												break;

												case '1':
													$itz['find.tech_1'] = PATH.MODULE.'tech/item_status/'.$d2.'/1';
													$itz['find.tech_2'] = PATH.MODULE.'tech/item_status/'.$d2.'/2';
												break;
											}

											if (count($itz) > 0)
												{
													$sa .= h(lang('find.send_to'),3);
													$sa .= $Style->menuBtn($itz);
												}
											
											$sz .= bsc($sa,10);
											break;

										case '1':
										/******************************************* CATALOG */
											$IDM = $Itens->create_rdf_work($d2);
											$Books = new \App\Models\Book\Books();
											$dt = $Itens->find($d2);
											$sz .= bsc(
												$Itens->header_item($dt).
												$Books->edit_rdf_item($IDM),10);
											break;
										default:
											$sz .= bsc(bsmessage('Tech '.$st.' not implemented'),10);
											break;

									}
							}
						
						$sx .= bs($sz);
						$sx .= '</div>';
						$sx .= '</div>';
						break;
					case 'prepare_I':
						/* Novos Itens */
						$Itens = new \App\Models\Library\Itens();
						$sa = '';
						$last = $Itens->last_aquisitions();
						$sa .= bsc($this->image_left(1).$last,2);
						$sa .= bsc($Itens->novo_item($d2,$d3,$d4),10);
						$sx .= bs($sa);
						break;
																		
					default:
						$sx .= bs($this->resume());
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
			$Itens = new \App\Models\Library\Itens();
			$Style = new \App\Models\Style\MenuBtn();
			$dt = $Itens->resume();

			$sx = '';			
			$sx .= bsc($this->image_left(0),2);

			/****************************************** ITEMS */
			$st = array('I',0,1,2);					
			$its = array();
			
			for ($r=0;$r < count($st);$r++)
				{
					$tot = 0;
					$se = '';
					for ($q=0;$q < count($dt);$q++)
						{
							if ($dt[$q]['i_status'] == $st[$r])
								{
									$tot = $dt[$q]['total'];
								}
						}
					if ($tot > 0)
						{
							$se = '<br><span class="pe-2 ps-2 btn-danger p-1 small rounded-3">'.$tot.'</span>';
						} else {
							$se .= '<br>&nbsp;</span>';
						}

					$mn = lang('find.tech_'.$st[$r]).$se;
					$its[$mn] = PATH.MODULE.'tech/prepare_'.$st[$r];
				}
			$url = PATH.MODULE;
			$sm = h(lang('find.techpreparation'),3);	
			$sx .= bsc($sm.$Style->menuBtn($its,$url),10);
			return $sx;
		}
}
