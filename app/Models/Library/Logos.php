<?php

namespace App\Models\Library;

use CodeIgniter\Model;

class Logos extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'logos';
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

	function logo($dt,$tp=0)
		{
			IF (is_array($dt))
				{
					$lib = $dt['l_code'];
				} else {
					$lib = $dt;
				}
							
			$lib = $dt['l_code'];
			$file = 'logo_$lib_mini.jpg';
			$file = 'img/logo/'.$file;
			$file = troca($file,'$lib',$lib);

			if (file_exists($file))
				{
					$file = URL.($file);					
				} else {
					$file = URL.('img/logo/no_logo.png');
				}
			$file = '<img src="'.$file.'" class="img-fluid"/>';			
			$tela = $file;

			return $tela;
		}

	function logo_mini($dt,$tp=0)
		{
			IF (is_array($dt))
				{
					$lib = $dt['l_code'];
				} else {
					$lib = $dt;
				}
			
			$file = 'logo_$lib_mini.jpg';
			$file = 'img/logo/'.$file;
			$file = troca($file,'$lib',$lib);

			if (file_exists($file))
				{
					$file = URL.($file);					
				} else {
					$file = URL.('img/logo/no_logo.png');
				}
			$file = '<img src="'.$file.'" style="height: 40px;"/>';			
			$tela = $file;

			return $tela;
		}		
}
