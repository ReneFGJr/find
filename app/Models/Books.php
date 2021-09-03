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

	var $dir_images = '../_covers/image/';


	function latest_acquisitions()
		{
			$sx = '';
			$Find_Item = new \App\Models\Find_Item();

			$Find_Item->select('i_library, id_i, i_titulo, i_identifier');
			$Find_Item->distinct();
			$Find_Item->where('i_library',LIBRARY);
			$Find_Item->orderBy('i_created DESC');
			$Find_Item->limit(18);
			$result = $Find_Item->Find();

			for($r=0;$r < count($result);$r++)
				{
					$line = $result[$r];
					//print_r($line);
					$file = $this->dir_images.$line['i_identifier'].'.jpg';
					if (file_exists($file))
						{
							$img = 'https://ufrgs.br/find/_covers/image/'.$line['i_identifier'].'.jpg';
						} else {
							$img = base_url('img/book/no_cover.jpg');
						}
					
					$sx .= $this->card($line,$img);
				}
			return $sx;			
		}

	function view($dt)
		{
			$RDFData = new \App\Models\RDFData();			
			$class = $dt['concept']['prefix_ref'].':'.$dt['concept']['c_class'];
			switch($class)
				{
					case 'frbr:Work':
						$sx = $this->viewWork($dt);
						$sx .= $RDFData->view_data($dt);						
					break;

					default:
					$sx = $this->viewWork($dt);
					$sx .= $RDFData->view_data($dt);
				}
			return $sx;
		}

	function viewWork($dt)
		{
			$sx = '';
			$class = $dt['concept']['prefix_ref'].':'.$dt['concept']['c_class'];
			$sx .= '<h1>'.$class.'</h1>';

			$sx = bs($sx);
			return $sx;
		}				

	function card($dt,$img='')
		{
			$title = $dt['i_titulo'];
			$st = '<a href="'.base_url(PATH.'v/'.$dt['id_i']).'">';
			$st .= '<img class="card-img-top" src="'.$img.'" alt="Card image cap">';
			$st .= '</a>';

			$sx = bsc(				 
						bscard($st,
									'<span class="book_title">'.$title.'</span>',
					)
				,2);			
			return $sx;
		}
}
