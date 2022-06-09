<?php

namespace App\Models\Book;

use CodeIgniter\Model;

class Covers extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'covers';
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

	function get_nail($id)
		{
			$RDFExport = new \App\Models\RDF\RDFExport();
			$Files = new \App\Models\Io\Files();

			$dir = $Files->directory($id);
			$file = $dir.'cover_nail.jpg';
			if (file_exists(($file)))
				{
					$sx = file_get_contents($file);
				} else {
					$img = $RDFExport->exportNail($id);						
					$sx = '<img src="'.$img.'" class="img-fluid p-3">';
					file_put_contents($file, $sx);
				}
			return ($sx);
		}

	function upload_cover($isbn='',$endPoint='')
		{
			/************************ Busca */
			if (($data = @file_get_contents($endPoint)) === false) {
				$error = error_get_last();
				echo "HTTP request failed. Error was: " . $error['message'];
			} else {
				$file = '_covers/image/'.$isbn.'.jpg';
				file_put_contents($file, $data);
			}	
		}

	function btn_cover($isbn)
		{
			$sx = '';
			$sx .= '<a href="'.base_url('covers/upload/'.$isbn).'" class="btn btn-primary btn-sm">';
			return $sx;
		}

	function get_cover($id)
		{
			$file = '_covers/image/'.$id.'.jpg';
			if (file_exists($file))
				{
					$img = URL.$file;
				} else {
					$file = '../../_covers/image/'.$id.'.jpg';
					if (file_exists($file))
					{
						$img = URL.$file;
					} else {
						$img = URL.('img/book/no_cover.jpg');
					}
				}
			return $img;
		}
}
