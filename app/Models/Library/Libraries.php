<?php

namespace App\Models\Library;

use CodeIgniter\Model;

class Libraries extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'library';
	protected $primaryKey           = 'id_l';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_l','l_name','l_code','l_id','l_logo','l_about','l_visible','l_net'
	];
	protected $typeFields        = [
		'hidden',
		'string:100',
		'string:10',
		'string:10',
		'hidden',
		'text',
		'sn',
		'string:10'
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

	function index($d1='',$d2='',$d3='',$d4='')
	{
		$tela = '';
		switch($d1)
		{
			case 'edit':
				$tela = bs(bsc($this->edit($d2),12));
				break;
			case 'viewid':
				$tela = bs(bsc($this->viewid($d2),12));
				break;
			default:
				$tela = bs(bsc($this->tableview(),12));
				break;
		}	
		return $tela;	
	}
	//http://find/public/index.php/find/config
	//http://find/public/index.php/index.php/find/config/edit/2
	//http://find/public/index.php/find/config//edit/2

	function logos($dt)
		{
			$Logos = new \App\Models\Library\Logos();
			$sx = '';
			$sx .= '<hr>';
			
			
			/****************************************************** Logo 150px */
			$link = onclick(PATH.MODULE.'admin/Library/Logos/1',800,400);
			$sa = '';
			$sa1 = h('Logo 150px',6);
			$sa1 .= $Logos->logo($dt);
			$sa1 .= '<br>';
			$sa1 .= $link.bsicone('upload').'</span>';
			$sa = bsc($sa1,6);
			
			/****************************************************** Logo 50px */
			$link = onclick(PATH.MODULE.'admin/Library/Logos/1',800,400);
			$sa1 = h('Logo 50px',6);
			$sa1 .= $Logos->logo_mini($dt);
			$sa1 .= '<br>';
			$sa1 .= $link.bsicone('upload').'</span>';
			$sa .= bsc('',2);

			/******************************************************** Mount */
			$sa .= bsc($sa1,4);

			$sx .= bs($sa);

			return $sx;					
		}

	function viewid($id)
		{
			$Logos = new \App\Models\Library\Logos();
			$Places = new \App\Models\Library\Places();
			$dt = $this->find($id);
			$tela = '';
			$tela1 = h($dt['l_name'],3);
			$tela1 .= '<p><sup>CODE: '.$dt['l_code'].'</sup></p>';
			$tela1 .= '<p>'.$dt['l_about'].'</p>';

			$tela2 = $Logos->logo($dt);

			$tela1 .= $Places->view_library($dt['l_code']);

			$tela1 .= $this->logos($dt);

			$tela2 .= '<hr>';
			if ($dt['l_visible'] == 1)
				{
					$tela2 .= bsmessage(lang('find.library_active'),1);
				} else {
					$tela2 .= bsmessage(lang('find.library_inative'),3);
				}

			$tela = bs(bsc($tela1,9).bsc($tela2,3));
			return $tela;
		}

	function edit($id)
		{
			$this->id = $id;
			$this->path = PATH.MODULE.'admin/Library';
			$this->path_back = PATH.MODULE.'admin/Library';
			$tela = form($this);
			return $tela;
		}

	function tableview()
		{
			$this->path = PATH.MODULE.'admin/Library';
			$tela = tableview($this);
			return $tela;
		}

}
