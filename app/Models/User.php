<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'users';
	protected $primaryKey           = 'id_us';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_us','us_nome','us_email'
	];

	protected $typeFields        = [
		'hi',
		'st100*',
		'st100*',
		'st100*',
		'st100*',
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

	function index($d1,$d2)
		{
			switch($d1)
				{
					case 'viewid':
						$sd = $this->card($d2);		
						$sr = $this->loan($d2);				
						$sx = bs(
								bsc($sr,8).
								bsc($sd,4)
								);
						break;

					default:
						$this->path = 'users/';
						$sx = bs(tableview($this));
						break;
				}
				return $sx;
		}
	function loan($id)
		{
			$Loan = new \App\Models\Loan();
			$ItemHistoric = new \App\Models\ItemHistoric();

			$sx = bsc(
					'<h3>'.lang("Loan").'</h3>'.
					$Loan->loan_in($id).
					$Loan->loan_out($id)
					,5);
			$sx .= bsc(
				'<h3>'.lang("Loan_history").'</h3>'.
				$Loan->loan($id).
				$ItemHistoric->history($id)
				,7);

			$sx = bs($sx);
			return $sx;
		}
	function card($id)
		{
			$UserAddress = new \App\Models\UserAddress();

			$dt = $this->find($id);
			$da = $UserAddress->where('ua_us',$id)->findAll();
			if (isset($da[0]))
				{
					$da = $da[0];
				}

			$sx = '<h4>'.lang('find.user.profile').'</h4>';

			$sn = '<span class="supersmall">'.lang('find.user_name').'</span><br/>';
			$sn .= '<span class="text-bold">'.$dt['us_nome'].'</span>';

			$st = $this->show_image($dt);

			/* Nascimento */
			if (isset($da['ua_nasc']) and ($da['ua_nasc'] != '1900'))
				{
					$st .= '<span class="text-bold small">';
					$st .= lang('find.born_date');
					$st .= '</span>';
					$st .= ': ';
					$st .= '<span class="small">';
					//$st .= date("d",$da['ua_nasc']);
					$data = strtotime($da['ua_nasc']);
					$st .= round(date("d",$data));
					$st .= ' ';
					$st .= lang('opendata.month_'.round(date("m",$data)));
					$st .= ' ';
					$st .= round(date("Y",$data));
					$st .= '</span>';
					$st .= '<hr>';
				}
			/******************************* Status  */
			$st .= $this->show_status($dt);

			/******************************* Endereço */
			$st .= '<span class="supersmall">'.lang('find.address').'</span><br/>';
			$st .= $da['us_logradouro'];
			if (strlen(trim($da['us_complemento'])) > 0)
				{
					$st .= ', '.trim($da['us_complemento']);
				}
			$st .= '<br>';

			if (strlen($da['us_bairro']) != '')
				{
					$st .= $da['us_bairro'] . ' - ';
				}

			if (strlen($da['us_localidade']) != '')
				{
					$st .= $da['us_localidade'];
				}

			if (strlen($da['us_uf']) != '')
				{
					$st .= ', '.$da['us_uf'];
				}
			$st .= '<br/>';
			if (strlen($da['us_cep']) != '')
				{
					$st .= 'CEP: '.$da['us_cep'] ;
				}

			/************************************ Contato */
			$st .= '<hr>';
			if (strlen($dt['us_email']) != '')
				{
					$st .= 'E-mail: '.$this->show_email($dt).'<br/>';
				}
			if (strlen($da['us_fone1']) != '')
				{
					$st .= lang('Phone').': '.$this->show_phone($da).'<br/>';
				}
				
			/************************************ Genere and Breed */
			$st .= '<hr>';
			$st .= '<span class="supersmall">'.lang('find.genere_and_breed').'</span>';
			$st .= '<table width="100%" border=0>';
			$st .= '<tr>';
			
			/* Genere */
			$st .= '<td width="50%" align="center">';
			$st .= '<img src="'.$this->show_genere($da).'" style="max-height: 100px;" title="'.lang('opendata.genere_'.$da['us_genero']).'">';
			$st .= '</td>';

			/* Breed */
			$st .= '<td width="50%" align="center">';
			$st .= '<img src="'.$this->show_breed($da).'" style="max-height: 100px;" title="'.lang('opendata.breed_'.$da['us_raca']).'">';
			$st .= '</td>';
			$st .= '</tr>';
			$st .= '</table>';

			/*
			echo '<pre>';
			print_r($dt);
			print_r($da);
			echo '</pre>';
			*/
			$sx .= bscard($sn,$st);

			return $sx;
		}

	function show_image($dt)
		{
			$Images = new \App\Models\Images();
			$dir = '_repository/users/';
			$id = $dt['id_us'];
			$picture = $Images->photo($id);
			$picture_take = base_url('img/other/take_picture.png');

			$st = '<table width="100%" cellpadding="4">';
			$st .= '<tr valign="top">';
			$st .= '<td width="85%">';
			$st .= '<img src="'.$picture.'" class="img-fluid">';
			$st .= '</td>';
			$st .= '<td width="15%">';
			$st .= '<img src="'.$picture_take.'" class="img-fluid">';
			$st .= '<tr>';
			$st .= '</table>';

			return $st;
		}

	function show_status($dt)
		{
			if ($dt['us_ativo'])
				{
					$sx = bsmessage(lang('find.user_status_'.$dt['us_ativo']),3);
				} else {
					$sx = bsmessage(lang('find.user_status_'.$dt['us_ativo']),6);
				}
			return $sx;
		}		
	function show_genere($dt)
		{
			$breed = $dt['us_genero'];
			switch ($breed)
				{
					case '1':
						$img = 'genere_woman.png';
						break;
					case '3':
						$img = 'genere_man.png';
						break;
					case '2':
						$img = 'genere_trans_woman.png';
						break;
					case '4':
						$img = 'genere_trans_man.png';
						break;
					case '5':
						$img = 'genere_not_binary.png';
						break;
					default:
						$img = 'genere_none.png';
						break;
				}
			$sx = base_url('img/genere/'.$img);
			return $sx;
		}

	function show_breed($dt)
		{
			$breed = $dt['us_raca'];
			switch ($breed)
				{
					case '1':
						$img = 'breed_black.png';
						break;
					case '2':
						$img = 'breed_brown.png';
						break;
					case '3':
						$img = 'breed_white.png';
						break;
					case '4':
						$img = 'breed_easten.png';
						break;
					case '5':
						$img = 'breed_indian.png';
						break;
					default:
						$img = 'breed_none.png';
						break;
				}
			$sx = base_url('img/breed/'.$img);				
			return $sx;
		}
	function show_phone($dt)
		{
			$sx = $dt['us_fone1'];
			return $sx;
		}
	function show_email($dt)
		{
			$sx = $dt['us_email'];
			return $sx;
		}
	function viewid($id)
		{
			$dt = $this->find($id);
			print_r($dt);
		}
}
