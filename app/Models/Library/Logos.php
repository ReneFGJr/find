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

	function upload($d1,$d2,$d3,$d4,$d5)
		{
			$sx = form_open_multipart('',array('name' => 'form_upload','id' => 'form_upload'));
			$sx .= form_upload("logo", "logo");
			$sx .= form_submit('action',lang('find.send'));
			$sx .= form_close();

			if (isset($_FILES['logo']))
				{
					$tmp = $_FILES['logo']['tmp_name'];
					dircheck('img/banners/'.LIBRARY);
					$mine = mime_content_type($tmp);
					echo $mine;
					switch($mine)
						{
							case 'image/png':
								$type = '.png';
								break;
							case 'image/jpg':
								$type = '.jpg';
								break;
							default:
								$sx = bsmessage(lang('find.file_format_invalid').' - '.$type,3);
								return $sx;

						}					
					$filename = '';
					
					switch($d1)
						{
							case 'Banner':
								$filename = 'img/banners/'.LIBRARY.'/banner_'.strzero($d2,2).$type;
								break;
							case 'mini':
								$filename = 'logo/logo_'.LIBRARY.'_mini'.$type;
								break;
							case 'logo':
								$filename = 'logo/logo_'.LIBRARY.$type;
								break;
							default:
								echo "FILE TYPE NOT FOUND - $d1 - $d2";
								exit;
						}	
					move_uploaded_file($tmp,$filename);
					$sx .= bsmessage(lang('find.file_uploaded').' - '.$filename,1);
				}			
			return $sx;
		}

	function banner()
		{
		$sx = '<div id="libraryBanners" class="carousel slide carousel-fade mb-5" data-bs-touch="false" data-bs-interval="false">
				<div class="carousel-inner">
					<div class="carousel-item active">
					<img src="'.URL.$this->banner_nr(LIBRARY,1).'" class="d-block w-100" alt="Banner one (1)">
					</div>
					<div class="carousel-item">
					<img src="'.URL.$this->banner_nr(LIBRARY,2).'" class="d-block w-100" alt="Banner one (2)">
					</div>
					<div class="carousel-item">
					<img src="'.URL.$this->banner_nr(LIBRARY,3).'" class="d-block w-100" alt="Banner one (3)">
					</div>
				</div>
				<button class="carousel-control-prev" type="button" data-bs-target="#libraryBanners" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Previous</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#libraryBanners" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Next</span>
				</button>
				</div>';
			$sx .= '<script> 
					var myCarousel = document.querySelector(\'#libraryBanners\')
					var carousel = new bootstrap.Carousel(myCarousel, {
  					interval: 5000
					}) </script>';
			return $sx;
		}	

	function banner_nr($library,$nr)
		{
			$sx = '';
			$img = 'img/banners/banner_01.png';

			$file = 'img/banners/'.$library.'/banner_'.strzero($nr,2).'.png';
			if (file_exists($file))
				{
					$img = $file;
				}
			return $img;
		}

	function logo($dt,$tp=0)
		{
			IF (is_array($dt))
				{
					$lib = $dt['l_code'];
				} else {
					$lib = LIBRARY;
				}
							
			$lib = $dt['l_code'];
			if ($tp==0)
				{
					$file = 'logo_$lib.jpg';
				} else {
					$file = 'logo_$lib_mini.jpg';
				}
			
			$file = 'img/logo/'.$file;
			$file = troca($file,'$lib',$lib);

			if (file_exists($file))
				{
					$file = URL.($file);					
				} else {
					$file = URL.('img/logo/no_logo.png');
				}
			$file = '<img src="'.$file.'" class="img-fluid" style="max-height: 150px;"/>';			
			$tela = $file;

			return $tela;
		}

	function logo_mini($dt,$tp=0)
		{
			IF (is_array($dt))
				{
					$lib = $dt['l_code'];
				} else {
					$lib = LIBRARY;
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
