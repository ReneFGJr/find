<?php
class books extends CI_model
{
	function search()
	{
		$form = new form;
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$A','',msg('search_ISBN'),true,true));
		array_push($cp,array('$S50','','ISBN',true,true));
		array_push($cp,array('$B8','',msg('search'),false,true));
		$sx = $form->editar($cp,'');

		return($sx);
	}

	function locate($isbn)
	{
		$sx = '';
		/* Google */
		$google = $this->google_api->book($isbn);
		$amazon = $this->amazon_api->book($isbn);
		/*
		echo '<pre>';
		print_r($google);
		echo '<hr>';
		print_r($amazon);
		echo '</pre>';
		*/

		/************************************** Título *****************/
		if (strlen($google['titulo']) > 0)
		{
			$title = $google['titulo'];
			$idioma = $google['expressao']['idioma'];
			$genere = $google['expressao']['genere'];
			$dt = $google;
		} else {
			if (strlen($amazon['titulo']) > 0)	
			{
				$title = $amazon['titulo'];
				$idioma = $amazon['expressao']['idioma'];
				$genere = $amazon['expressao']['genere'];
				$dt = $amazon;
			}
		}
		if (strlen($title) > 0)
		{
			$isbns = $this->isbn->isbns($isbn);
			$dt['cover'] = $amazon['cover'];
			$dt['editora'] = $amazon['editora'];
			$dt['isbn13'] = $isbns['isbn13'];
			$dt['isbn10'] = $isbns['isbn10'];
			$dt['work'] = $this->work($title,$idioma);
			$dt['expression'] = $this->expression($dt['work'],$idioma,$genere);
			$dt['manifestation'] = $this->manifestation($dt['expression'],$dt);
			$this->covers->save($dt['cover'],$dt['manifestation']);
		}
		return($sx);
	}

	/***************************************************************************** WORK ****************/
	function work($title,$language)
	{
		/* Limpa titulo */
		while (strpos('_'.$title,'  ') > 0)
		{
			$title = troca($title,'  ',' ');
		}

		$sql = "select * from find_work where w_title = '".$title."'";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) == 0)
		{
			$xsql = "insert into find_work
			(w_title)
			values
			('$title')";
			$xrlt = $this->db->query($xsql);					
			sleep(1);
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
		}
		
		if (count($rlt) == 0)
		{
			echo "OPS - ERRO DE GRAVAÇÃO";
		}
		$id = $rlt[0]['id_w'];
		return($id);
	}


	/***************************************************************************** EXPRESSION ***********/
	function expression($w,$language,$type)
	{

		$lang = $this->languages->code($language);
		if (strlen($lang) == 0)
		{
			echo "OPS NOT LINGUAGE ".$language;
			exit;
		}

		$gen = $this->generes->code($type);
		if (strlen($gen) == 0)
		{
			echo "OPS NOT GENERE ".$gen;
			exit;
		}

		$sql = "select * from find_expression where e_work = '".$w."' and e_language = '$lang' and e_type = '$gen' ";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) == 0)
		{
			$xsql = "insert into find_expression
			(e_work, e_language, e_type)
			values
			('$w','$lang','$gen')";

			$xrlt = $this->db->query($xsql);					
			sleep(1);
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();			
		}

		if (count($rlt) == 0)
		{
			echo "OPS - ERRO DE GRAVAÇÃO";
		}
		$id = $rlt[0]['id_e'];
		return($id);
	}

	/***************************************************************************** EXPRESSION ***********/
	function manifestation($e,$d)	
	{
		$isbn13 = $d['isbn13'];
		$data = $d['data'];

		/********************** WHERE *********/
		$wh = '';
		if ($data > 1900)
		{
			$wh = "and (m_year = '$data')";	
		}
		
		/********************** CONSULTA ************/
		$sql = "select * from find_manifestation where m_isbn13 = '$isbn13' $wh";
		$rlt = $this->db->query($sql);
		$rlt = $rlt->result_array();
		if (count($rlt) == 0)
		{
			$xsql = "insert into find_manifestation
			(m_isbn13, m_expression, m_year)
			values
			('$isbn13','$e','$data')";

			$xrlt = $this->db->query($xsql);					
			sleep(1);
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();			
		}

		if (count($rlt) == 0)
		{
			echo "OPS - ERRO DE GRAVAÇÃO";
		}
		$id = $rlt[0]['id_m'];
		return($id);
	}

}
?>