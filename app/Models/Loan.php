<?php

namespace App\Models;

use CodeIgniter\Model;

class Loan extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'find_item';
	protected $primaryKey           = 'id_i';
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

	function loan($id)
		{
			$this->where('i_usuario',$id);
			$this->where('i_status',6);
			$dt = $this->findAll();

			$sx = '<table width="100%">';
			$sx .= '<tr>	<th width="60%">'.lang('find.title').'</th>
							<th width="20%">'.lang('find.prev_deve').'</th>
							<th width="20%">'.lang('find.status').'</th>
					</tr>';
			foreach($dt as $id=>$line)
				{
					$dv = $line['i_dt_prev'];
					$sca = '</span>';
					if ($dv >= date("Ymd"))
						{
							$sc = '<span class="text-success">';
							$txt = lang("find.NORMAL");
						} else {
							$sc = '<span class="text-danger">';
							$txt = lang("find.LATE");
						}

					$sx .= '<tr>';
					$sx .= '<td>'.$sc.$line['i_titulo'].$sca.'</td>';
					$sx .= '<td>'.$sc.stodbr($line['i_dt_prev']).$sca.'</td>';
					$sx .= '<td>'.$sc.'<b>'.$txt.$sca.'</b>'.'</td>';
					$sx .= '</tr>';
				}
			$sx .= '</table>';
			return $sx;
		}

	function loan_out()
		{
				$sx = '
							<div style="margin-bottom: 20px; background-color: #FF6347; border: 0px solid #000000; padding: 15px; border-radius: 10px;">
								<h1>Empréstimo</h1><form method="post">
								<table><tbody><tr>
								<td>Informe o tombo:</td>
								<td><input type="text" name="tombo"></td>
								</tr></tbody></table>
								</form>
							</div>';
				return $sx;
		}

	function loan_in()		
		{
			$sx = ' 
							<div style="margin-bottom: 20px; background-color: #63AF47; border: 0px solid #000000; padding: 15px; border-radius: 10px;">
								<h1>Devolução</h1><form method="post">
								<table><tbody><tr>
								<td>Informe o tombo:</td>
								<td><input type="text" name="tomboDEV"></td>
								</tr></tbody></table>
							</form>
							</div>';
				return $sx;							
		}
}
