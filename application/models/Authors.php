<?php
class authors extends CI_model
{
	function le_authors($idw)
		{
			$sql = "select * from find_work_authors
						INNER JOIN find_authors ON wa_author = id_a
						where wa_work = $idw
						order by wa_order, id_wa";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			return($rlt);						
		}
	/***************************************************************** Authors - WORK ***/
	function authors_save($dt)
	{
		$authors = $dt['authors'];
		for ($r=0;$r < count($authors);$r++)
		{
			$nome = $authors[$r];
			$nome = troca($nome,"'","´");
			$sql = "select * from find_authors where a_name = '$nome' ";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();

			if (count($rlt) == 0)
			{
				$xsql = "insert into find_authors
				(a_name)
				values
				('$nome')";
				$xrlt = $this->db->query($xsql);
				sleep(1);
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
			}
			$ida = $rlt[0]['id_a'];
			$ord = ($r+1);
			$idw = $dt['work'];
			$sql = "select * from find_work_authors 
						where wa_work = $idw 
						and wa_author = $ida ";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			if (count($rlt) == 0)
			{
				$sql = "insert into find_work_authors 
				(wa_work, wa_author, wa_order)
				values
				('$idw','$ida','$ord')";
				$rlt = $this->db->query($sql);
			}
		}
	}
}
?>	