<?php

namespace App\Models\API;

use CodeIgniter\Model;

class BMS extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'googles';
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

function book($isbn,$id) {
		$rsp = array('count' => 0);

		$endPoint = 'http://brapci3/api/book/' . $isbn . '/' . $id;

		$ISBN = new \App\Models\Isbn\Isbn();
		$Language = new \App\Models\Languages\Language();		

		/************************ Busca */
		echo $endPoint;
		if (($data = @file_get_contents($endPoint)) === false) {
			$error = error_get_last();
			echo "HTTP request failed. Error was: " . $error['message'];
	  	} else {
			$rsp = json_decode($data, true);
	  	}		
		return ($rsp);
	}	
}
