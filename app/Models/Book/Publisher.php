<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Publisher extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'publishers';
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

	function viewManifestations($dt)
		{
			$Itens = new \App\Models\Library\Itens();
			$tela1 = '';
			$tela2 = $this->showPublishers($dt);
			$tela2 .= $Itens->showItens($dt);
			

			$sx = bs(bsc($tela2,10).bsc($tela1,2));
			return $sx;
		}

	function showPublishers($dt)
		{
			$title = $dt['concept']['n_name'];
			$sx = '<div class="publisher.title">'.h($title,2).'</div>';
			return $sx;
		}
}
