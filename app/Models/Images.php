<?php

namespace App\Models;

use CodeIgniter\Model;

class Images extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = '';
	protected $primaryKey           = '';
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

	var $maxsize = 300;

	function resize($filename,$maxw=0,$maxh=0)
		{
			list($width, $height) = getimagesize($filename);

			echo '=W=>'.$width.'<br>';
			echo '=H=>'.$height.'<br>';
		}

	function check($dir)
		{
			$tot = 0;
			$toti = 0;
			$max = $this->maxsize;
			$files = scandir($dir);
			$sx = '<h1>Images Check</h1>';
			$sx .= 'Max width: '.$max.'px<br>';
			$sx .= 'Total of files: '.count($files).'<br>';
			$sx .= '<ol>';
			foreach($files as $id => $filename)
				{
					if (($filename == '.') or ($filename == '..'))
						{
							// Ignore
						} else {
							$rst = ' ';
							$file = $dir.$filename;
							$fileo = $dir.'_'.$filename;
							$i = getimagesize($file);
							$width = $i[0];
							$height = $i[1];
							$mime = $i['mime'];
							$rst .= "($width x $height) - ".$mime;
							$rst .= ' '.number_format(filesize($file)/1024,1,',','.').'k Bytes';
							//echo $rst;
							$sit = '<span>';
							if ($width != $max) 
								{
									$sit = '<span style="color: red; weigth: bold;">';
									$sx .= '<li>'.$sit.$filename.$rst.'</span></li>';

									$newwidth = $max;
									if ($width == 0)
										{
											$rst .= 'ERRO NO ARQUIVO ';
											break;
										}
									$newheight = round(($height * $max) / $width);
									switch($mime)
										{
											case 'image/png':
												$source = imagecreatefrompng($file);
												break;
											case 'image/webp':
												$source = imagecreatefromwebp($file);
												break;
											case 'image/gif':
												$source = imagecreatefromgif($file);
												break;																								
											case 'image/bmp':
												$source = imagecreatefrombmp($file);
												break;												
	
											default:
											$source = imagecreatefromjpeg($file);
											break;
										}
									
									$thumb = imagecreatetruecolor($newwidth, $newheight);
									imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
									imagedestroy($source);
									imagejpeg($thumb,$file,100);
									imagedestroy($thumb);
									$tot++;
								}							
						}	
					$toti++;				
					if ($tot >= 25) { $sx .= bsmessage('have more...'.$toti.'/'.count($files)); break; }
				}
			$sx .= '</ol>';
			if ($tot > 0)
				{
					$sx .= metarefresh('',1);
				}
			return $sx;
		}
}
