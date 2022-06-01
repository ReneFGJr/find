<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemHistoric extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'find_item_historic_loan';
	protected $primaryKey           = 'id_hl';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_hl','hl_item','h_status','h_ip','h_user'
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

	function history($id)
		{
			$this->select("i_titulo, i_manitestation, i_tombo, hl_date, hl_status, hl_ip, hl_created");
			$this->join('itens', 'hl_item = i_tombo', 'LEFT');
			$this->where('hl_user',$id);
			$this->where('hl_library',LIBRARY);
			$this->orderBy('hl_created DESC');
			$dt = $this->findAll(0,5);
		}
}
