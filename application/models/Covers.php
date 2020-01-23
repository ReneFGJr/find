<?php
class covers extends CI_model
{
	function img($i)
	{
		$file = $this->img_name($i);
		if (file_exists($file))
		{
			$img = base_url($file);
		} else {
			$img = base_url('img/no_cover.png');
		}
		return($img);
	}
	function img_name($i)
	{
		$file = '_covers';
		check_dir($file);
		$file .= '/image';
		check_dir($file);
		$file .= '/'.strzero($i,8).'-'.substr(md5($i),4,8).'.jpg';
		return($file);
	}

	function save($url,$m)
	{
		$img = $this->img_name($m);
		if (strlen($url) > 0)
		{
			$t = read_link($url);
			if (strlen($t) > 0)
			{
				file_put_contents($img, $t);
			}
		}
		return(0);
	}
}
?>	