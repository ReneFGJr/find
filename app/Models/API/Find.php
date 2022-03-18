<?php

namespace App\Models\API;

use CodeIgniter\Model;

class Find extends Model
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
		'i_identifier'
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

	function process($hv,$id)
		{
			$Tombo = new \App\Models\Book\Tombo();
			$Itens = new \App\Models\Book\Itens();
			
			$dd['i_manitestation'] = $hv['i_manitestation'];
			$dd['i_titulo'] = $hv['i_titulo'];
			$dd['i_year'] = $hv['i_year'];
			$dd['i_ln1'] = $hv['i_ln1'];
			$dd['i_ln2'] = $hv['i_ln2'];
			$dd['i_ln3'] = $hv['i_ln3'];
			$dd['i_ln4'] = $hv['i_ln4'];
			$dd['i_work'] = $hv['i_work'];
			$dd['i_library_classification'] = $hv['i_library_classification'];

			$Tombo->set($dd)->where('id_i',$id)->update();
			$Itens->status($id,2);
			$sx = bsmessage(lang('find.recover_other_items'),1);
			return $sx;
		}

	function book($isbn,$id) {

		$ISBN = new \App\Models\Isbn\Isbn();
		$Language = new \App\Models\Languages\Language();
		$rsp = array();

		$dt = $this
			->where('i_identifier',$isbn)
			->where('i_manitestation > 0')
			->findAll();

		if (count($dt) == 0)
			{
				return array();
			}
		else {
			$dd['manitestation'] = $dt[0]['i_manitestation'];
			return $dt[0];
		}
		
	}
}
