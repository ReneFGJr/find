<?php
class sourcers extends CI_model
{
	function show()
	{
		$sql = "select * from sources where s_type like '%marc%' order by s_name ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		$sx = '';
		for ($r=0;$r < count($rlt);$r++)
		{
			$line = $rlt[$r];
			$sx .= '<div class="col-2 text-center" style="margin-top: 30px;">';
			$sx .= '<a href="'.$line['s_link'].'" target="_new">';
			$sx .= '<img src="'.base_url($line['s_logo']).'" align="left" class="img-fluid">';
			$sx .= '<br>';
			$sx .= '<span class="small">'.$line['s_name'].'</span>';
			$sx .= '</a>';
			$sx .= '</div>';
		}
		return($sx);
	}
}
?>