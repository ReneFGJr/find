<?php
class sourcers extends CI_model
{
	function fontes()
	{
		$sql = "select * from sources where s_type like '%marc%' order by s_name ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<div>';
			$sx .= '<img src="'.base_url($line['s_logo']).'" height="80" align="left" style="padding: 5px 10px;">';
			$sx .= '<span class="big">'.$line['s_name'].'</span>';
			$sx .= '<br><a href="'.$line['s_link'].'" target="_new">[LINK]</a>';
			$sx .= '</div>';
		}
		return($sx);
	}
}
?>