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

	function upload($d1, $d2 = '')
	{
		$sx = '';
		$sx .= h(lang('find.logo_' . $d1), 2);
		$sx .= form_open_multipart('', array('name' => 'form_upload', 'id' => 'form_upload'));
		$sx .= form_upload("logo", "logo");
		$sx .= form_submit('action', lang('find.send'));
		$sx .= form_close();

		if (isset($_FILES['logo'])) {
			$tmp = $_FILES['logo']['tmp_name'];
			dircheck('img/banners/' . LIBRARY);
			$mine = mime_content_type($tmp);
			switch ($mine) {
				case 'image/png':
					$type = '.png';
					break;
				case 'image/jpg':
					$type = '.jpg';
					break;
				case 'image/jpeg':
					$type = '.jpg';
					break;
				default:
					$sx = bsmessage(lang('find.file_format_invalid') . ' - ' . $mine, 3);
					return $sx;
			}
			$filename = '';

			if ($d1 == 'logo') {
				$d2 = 'logo';
			}

			switch ($d2) {
				case '1':
					$filename = 'img/banners/' . LIBRARY . '/banner_' . strzero($d2, 2) . $type;
					break;
				case '2':
					$filename = 'img/banners/' . LIBRARY . '/banner_' . strzero($d2, 2) . $type;
					break;
				case '3':
					$filename = 'img/banners/' . LIBRARY . '/banner_' . strzero($d2, 2) . $type;
					break;
				case 'mini':
					$filename = 'img/logo/logo_' . LIBRARY . '_mini' . $type;
					break;
				case 'logo':
					$filename = 'img/logo/logo_' . LIBRARY . $type;
					break;
				default:
					echo "<hr>FILE TYPE NOT FOUND - $d1 - $d2";
					exit;
			}
			move_uploaded_file($tmp, $filename);
			$sx .= bsmessage(lang('find.file_uploaded') . ' - ' . $filename, 1);
			$sx .= wclose();
		}
		return $sx;
	}

	function banner()
	{
		$hd = 'd-none d-lg-block';
		$sx = '<div id="libraryBanners" class="' . $hd . ' carousel slide carousel-fade mb-5" data-bs-touch="false" data-bs-interval="false">
				<div class="carousel-inner">
					<div class="carousel-item active">
					<img src="' . URL . $this->banner_nr(LIBRARY, 1) . '" class="d-block w-100" alt="Banner one (1)">
					</div>
					<div class="carousel-item">
					<img src="' . URL . $this->banner_nr(LIBRARY, 2) . '" class="d-block w-100" alt="Banner one (2)">
					</div>
					<div class="carousel-item">
					<img src="' . URL . $this->banner_nr(LIBRARY, 3) . '" class="d-block w-100" alt="Banner one (3)">
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

	function banner_nr($library, $nr)
	{
		$sx = '';
		$img = 'img/banners/banner_01.png';

		$file = 'img/banners/' . $library . '/banner_' . strzero($nr, 2) . '.png';
		if (file_exists($file)) {
			$img = $file;
		}
		$file = 'img/banners/' . $library . '/banner_' . strzero($nr, 2) . '.jpg';
		if (file_exists($file)) {
			$img = $file;
		}
		return $img;
	}

	function logo($dt, $tp = 0)
	{
		if (is_array($dt)) {
			$lib = $dt['l_code'];
		} else {
			$lib = LIBRARY;
		}

		$lib = $dt['l_code'];
		if ($tp == 0) {
			$file = 'logo_$lib.jpg';
		} else {
			$file = 'logo_$lib_mini.jpg';
		}
		$file = 'img/logo/' . $file;
		$file = troca($file, '$lib', $lib);

		if (!file_exists($file)) {
			if ($tp == 0) {
				$file = 'logo_$lib.png';
			} else {
				$file = 'logo_$lib_mini.png';
			}
			$file = 'img/logo/' . $file;
			$file = troca($file, '$lib', $lib);
		}

		if (file_exists($file)) {
			$file = URL . ($file);
		} else {
			$file = URL . ('img/logo/no_logo.png');
		}
		$file = '<img src="' . $file . '" class="img-fluid" style="max-height: 150px;"/>';
		$tela = $file;

		return $tela;
	}

	function logo_mini($dt, $tp = 0)
	{
		if (is_array($dt)) {
			$lib = $dt['l_code'];
		} else {
			$lib = LIBRARY;
		}


		$file = 'logo_$lib_mini.jpg';
		$file = 'img/logo/' . $file;
		$file = troca($file, '$lib', $lib);

		if (file_exists($file)) {
			$file = URL . ($file);
		} else {
			$file = URL . ('img/logo/no_logo.png');
		}
		$file = '<img src="' . $file . '" style="height: 40px;"/>';
		$tela = $file;

		return $tela;
	}
}
