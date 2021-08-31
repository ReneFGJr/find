<?php
/*
Offset 	Tamanho 	Descrição
0 		5		Tamanho do registro
5 		1		Situação do registro
6 		4 		Códigos de implementação
10 		1 		Tamanho do indicador
11 		1 		Tamanho do identificador de sub-campo
12 		5 		Endereço base dos dados
17 		3 		Para sistemas de usuários
20 		1 		Tamanho do campo “Tamanho da entrada”
21 		1 		Tamanho da posição de caractere inicial
22 		2 		Para uso futuro 

003 - Etiqueta

*/
class ISO2709 extends CI_model {
	var $txt = '';
	var $row = '';
	function import($file)
	{
		$sx = '<ol>';
		$sx2 = '<ol>';
		if (file_exists($file))
		{
			/* Le arquivo linha a linha */
			$fn = fopen($file,"r");		
			while(! feof($fn))  {
				$t = fgets($fn);
				if (strpos($t,chr(9)) > 0)
				{
					$pos = strpos($t,chr(9));
					if ($pos < 10)
						{
							$t = substr($t,$pos+1,strlen($t));
						}
				}
				$dt = $this->register_header($t);
				if (isset($dt['isbn']['isbn13']))
				{
					$isbn = $dt['isbn']['isbn13'];
					if ((substr($isbn,0,3) == '978') and (strlen($isbn) == 13))
					{
						$sx .= '<li>'.$dt['isbn']['isbn13'].'<br>'.'</li>';
						$this->row .= $dt['isbn']['isbn13'].';';
					} else {
						$sx2 .= '<hr>';
						$sx2 .= $dt['marc'];
					}
				}
			}
			fclose($fn);
		} else {
			$sx = message(msg('file_not_found').' '.$file,3);
		}
		$sx .= '</ol>';
		$sx2 .= '</ol>';
		$tela = '<div class="container"><div class="row">';
		$tela .= '<div class="col-md-3">'.$sx.'</div>';
		$tela .= '<div class="col-md-9"><pre>'.$sx2.'</pre></div>';
		$tela .= '</div></div>';
		return($tela);
	}
	
	function register_header($z)
	{
		$z = trim($z);
		
		$rg = array();
		$rg['size'] = round(substr($z,0,5));
		$rg['status'] = substr($z,5,1);
		$rg['code_impl'] = substr($z,6,4);
		$rg['indicator_size'] = substr($z,10,1);
		$rg['indicator_width'] = substr($z,10,1);
		$rg['data_pos'] = substr($z,12,5);
		$rg['user_sis'] = substr($z,17,3);
		$rg['field_size'] = substr($z,20,1);
		$rg['char_size'] = substr($z,21,1);
		$rg['in_size'] = substr($z,22,1);
		$rg['nouser'] = substr($z,23,1);
		
		$reg = substr($z,24,strlen($z));
		$rg['size_2'] = strlen($reg).' - '.(strlen($reg)-$rg['size']);
				
		if (($rg['status'] != 'n') and ($rg['status'] != 'c'))
		{	
			echo "OPS";	
			
			echo '=>'.strlen($reg).'<br>';
			echo '<tt style="color: blue;">'.'{'.substr($z,0,25).'}</tt>';
			echo '<tt style="color: green;>'.'['.$reg.']</tt>';
			echo '<hr>';
			echo '<pre>';
			print_r($rg);
			echo '</pre>';	
			return(array());	
			exit;
		}
		
		
		$regs = $this->register_fields($reg,$rg['field_size'],$rg['data_pos'],$rg['size']);
		$rg['isbn'] = $this->isbn_marc($regs);
		$rg['marc_array'] = $regs;
		$rg['marc'] = $this->marc($regs);
		$isbn = $rg['isbn']['isbn13'];
				
		/* salva arquivo na pasta para recuperação */
		if ((strlen($rg['marc']) > 0) and (strlen($isbn) == 13))
		{
			if (substr($isbn,0,3) == '978')
			{
				$this->marc_api->save_marc($isbn,$rg['marc']);
			}
		} else {
			$this->txt .= $rg['marc'].'<hr>';
		}			
		return($rg);
	}
	
	function isbn_marc($rg)
	{
		$isbn = '';
		for ($r=0;$r < count($rg);$r++)
		{
			$line = $rg[$r];
			if ($line[0] == '020')
			{
				$isbn = substr($line[3],4,strlen($line[3]));
			}
			
		}
		return($this->isbn->isbns($isbn));
	}
	
	function marc($a)
	{
		$sx = '';
		for ($r=0;$r < count($a);$r++)
		{				
			$line = $a[$r];
			$vlr = $line[3];
			$vlr = troca($vlr,chr(30),'$');
			$vlr = troca($vlr,chr(29),'$');
			$vlr = troca($vlr,chr(31),'$');
			$field = $line[0];
			$ind1 = '';
			$ind2 = '';
			
			switch($field)
			{
				case '001':
				break;
				
				case '005':
				break;
				
				case '008':
				break;
				
				default:
				$ind1 = substr($vlr,0,1);
				$ind2 = substr($vlr,1,1);
				$vlr = substr($vlr,2,strlen($vlr));
			break;
		}
		$sx .= $field.' '.$ind1.$ind2.' '.$vlr.cr();
		/* Sub campos */
	}
	return($sx);
}

function register_fields($z,$sz,$fim,$fz)
{
	$fz = $fz - 25;
	$fim = $fim - 25;
	$nr = 3 + $sz + 5;
	$rg = array();
	$ini = 0;
	for ($r=0;$r < 200;$r++)
	{
		$rf = array();
		$rf[0] = substr($z,$ini+0,3);
		$rf[1] = substr($z,$ini+3,$sz);
		$rf[2] = substr($z,$ini+3+$sz,5);
		$rf[3] = 'null';
		array_push($rg,$rf);
		$ini = $ini + $nr;
		if ($ini >= $fim)
		{ $r = 999; }
	}
	for ($r=0;$r < (count($rg));$r++)
	{
		$pos = round($rg[$r][2])+$ini;
		if ($r == (count($rg)-1))
		{
			$size = 20;
			$tx = substr($z,$pos,$size);
			$size = ($fz - $pos);
		} else {
			$size = round($rg[$r+1][2]-$rg[$r][2]);
		}
		
		$tx = substr($z,$pos,$size);
		$tx = troca($tx,chr(30),'');
		$rg[$r][3] = $tx;
	}
	return($rg);
}

};
