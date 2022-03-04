<?php

namespace App\Models\Languages;

use CodeIgniter\Model;

class Language extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'languages';
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

	function form()
	{
		$o = array('pt' => 'Português', 'en' => 'Inglês', 'es' => 'Espanhol', 'fr' => 'Frances', 'ge' => 'Alemão');
		$sx = '';
		foreach ($o as $key => $value) {
			if ($sx != '') {
				$sx .= '&';
			}
			$sx .= $key . ':' . $value;
		}
		return ($sx);
	}
	function code($l)
	{
		$l = trim($l);
		$o = array('Português' => 'pt', 'pt_BR' => 'pt', 'en' => 'en', 'por' => 'pt', 'português' => 'pt', 'portugues' => 'pt');
		foreach ($o as $key => $value) {
			if ($l == $key) {
				return ($value);
			}
		}
		return ('');
	}
}
