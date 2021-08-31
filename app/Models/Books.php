<?php

namespace App\Models;

use CodeIgniter\Model;

class Books extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'books';
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


	function latest_acquisitions()
		{
			$sx = '';
			$Find_Item = new \App\models\Find_Item();

			$Find_Item->where('i_library',LIBRARY);
			$Find_Item->limit(12);
			$result = $Find_Item->Find();

			for($r=0;$r < count($result);$r++)
				{
					$line = $result[$r];
					$sx .= $this->card($line);
				}
			return $sx;			
		}

	function card($dt,$img='')
		{
			$title = $dt['i_titulo'];
			$st = '<a href="'.base_url(PATH.'v/'.$dt['id_i']).'">';
			$st .= '<img class="card-img-top" src="'.base_url('img/book/1011000326885.jpg').'" alt="Card image cap">';
			$st .= '</a>';

			$sx = bsc(				 
						bscard($st,
									'<span class="book_title">'.$title.'</span>',
					)
				,2);			
			return $sx;
		}
}
