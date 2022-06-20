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

	var $endpoint = 'http://brapci3/api/book/';

	function cutter($name = '')
	{
		$endPoint =  $this->endpoint . 'cutter/?q='.$name;
		echo $endPoint;
	}

	function cover($isbn = '')
	{
		$endPoint =  $this->endpoint . 'cover/150/?q='.$name;
		echo $endPoint;
	}	

	function status()
	{

		$endPoint =  $this->endpoint . $isbn . '/' . $id;
	}

	function book($isbn, $id)
	{
		$Cover = new \App\Models\Book\Covers();
		$rsp = array('count' => 0);

		$endPoint = $this->endpoint  . $isbn . '/' . $id;

		$ISBN = new \App\Models\Isbn\Isbn();
		$Language = new \App\Models\Languages\Language();

		/************************ Busca */
		if (($data = @file_get_contents($endPoint)) === false) {
			$error = error_get_last();
			echo "HTTP request failed. Error was: " . $error['message'];
		} else {
			$rsp = json_decode($data, true);
			if (isset($rsp['cover']) and ($rsp['cover'] != '')) {
				$endPoint = $rsp['cover'];
				$Cover->upload_cover($rsp['isbn13'], $endPoint);
			}
		}
		return ($rsp);
	}
}
