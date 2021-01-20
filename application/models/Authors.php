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
	function authors_save($dt,$idw)
	{
		$authors = $dt['authors'];
		$rdf = new rdf;
		for ($r=0;$r < count($authors);$r++)
		{
			$nome = $authors[$r];
			$nome = troca($nome,"'","´");
			
			$ida = $rdf->rdf_concept_create('Person', $nome, '', 'pt');
			$rdf->set_propriety($idw,'hasAuthor',$ida);
		}
	}

	function agents_save($dt,$idm)
	{
		$authors = $dt['agents'];
		$rdf = new rdf;
		for ($r=0;$r < count($agents);$r++)
		{
			$nome = $agents[$r];
			$nome = troca($nome,"'","´");
			
			$ida = $rdf->rdf_concept_create('CorporateBody', $nome, '', 'pt');
			$rdf->set_propriety($idm,'hasAuthor',$ida);
		}
	}	
}
