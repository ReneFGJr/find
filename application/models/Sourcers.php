<?php
class sourcers extends CI_model
{
	function show($isbn)
	{
		$sql = "select * from sources where s_type like '%marc%' order by s_name ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '<div class="row" style="border-top: 1px solid #000000; margin-top: 30px;">';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<div class="col-2	 text-center" style="margin-top: 10px;">';
			$link = $line['s_link'];
			$link = troca($link,'$isbn',$isbn);
			$sx .= '<a href="'.$link.'" target="_new">';
			$sx .= '<img src="'.base_url($line['s_logo']).'" align="left" class="img-fluid">';
			$sx .= '<br>';
			$sx .= '<span class="small">'.$line['s_name'].'</span>';
			$sx .= '</a>';
			$sx .= '</div>';
		}
		$sx .= '</div>';
		return($sx);
	}
}
?>