<?php
class cutters extends CI_model
	{
		function form($name='')
			{
				$cp = array();
				array_push($cp,array('$H','','',false,false));
				array_push($cp,array('$A','',msg('cutter_search'),False,True));
				array_push($cp,array('$S','','Nome do autor',True,True));
				$form = new form;
				$tela = $form->editar($cp,'');
				return($tela);
			}
		function find_cutter($name='')
			{
				$nm = nbr_author($name,4);
				$nm = nbr_author($nm,1);
				
				$sql = "select * from cutter where cutter_abrev like '".substr($nm,0,2)."%' order by cutter_abrev";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
				$cutter = '';
				for ($r=0;$r < count($rlt);$r++)
					{
						$line = $rlt[$r];
						$cp = UpperCaseSql($line['cutter_abrev']);
						$ok = 1;
						for ($q=0;$q < strlen($cp);$q++)
							{
								$cpa = substr($cp,$q,1);
								$cpb = substr($nm,$q,1);
								if ($cpb >= $cpa)
									{
										//echo '['.$cpa.'-'.$cpb.']';
									} else {
										//echo '{'.$cpa.'-'.$cpb.'}';
										$ok = 0;
									}
							}
						if ($ok ==1)
							{
								//echo '<<<======';
								$cutter = substr($nm,0,1).$line['cutter_code'];
								$cutter_name = $line['cutter_abrev'];								
							}
						
						
						//echo '<br>'.$nm;
						//echo '<br>'.$cp.'<br>';
					}
				
				$sx = '<code>';
				$sx .= $cutter.' '.$cutter_name;
				$sx .= '</code>';
				return($cutter);
			}
	}
