<?php
// This file is part of the Brapci Software. 
// 
// Copyright 2015, UFPR. All rights reserved. You can redistribute it and/or modify
// Brapci under the terms of the Brapci License as published by UFPR, which
// restricts commercial use of the Software. 
// 
// Brapci is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details. 
// 
// You should have received a copy of the Brapci License along with the Brapci
// Software. If not, see
// https://github.com/ReneFGJ/Brapci/tree/master//LICENSE.txt 
/* @author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
 * @date: 2015-12-01
 */
$lg = 'pt_BR';
if (!function_exists(('msg_lista')))
    {
        function msg_lista()
            {
                $CI = &get_instance();
                        $lg = 'pt_BR';
                        $sql = "select * from msg where msg_language = '$lg' order by msg_term";
                        $rlt = $CI->db->query($sql);
                        $rlt = $rlt->result_array();
                        $sx = '';
                        $sx .= '<table width="100%">';
                        $sx .= '<tr>
                                    <th>'.msg('label').'</th>
                                    <th>'.msg('description').'</th>
                                </tr>'; 
                                
                        for ($r=0;$r < count($rlt);$r++)
                            {
                                $line = $rlt[$r];
                                $link = '<span style="cursor: pointer;" onclick="newwin(\''.base_url('index.php/main/pop_config/msg/'.$line['id_msg']).'\',800,600);">';
                                $linka = '</span>';
                                $sx .= '<tr style="border-top: 1px solid #a0a0a0;">';
                                $sx .= '<td>';
                                $sx .= $link.$line['msg_term'].$linka;
                                $sx .= '</td>';

                                $sx .= '<td>';
                                $sx .= $link.$line['msg_label'].$linka;
                                $sx .= '</td>';
                                                                
                                $sx .= '</tr>'.cr();
                            }
                        $sx .= '</table>';
                        return($sx); 
            }
    }
if (!function_exists(('msg')))
	{
		function msg($t)
			{
			    //$t='MAKE_MESSAGES';
				$CI = &get_instance();
                /****** GERAR DICIONARIO ****/
                if ($t=='MAKE_MESSAGES')
                    {
                        $lg = 'pt_BR';
                        $sql = "select * from msg where msg_language = '$lg' ";
                        $rlt = $CI->db->query($sql);
                        $rlt = $rlt->result_array();
                        $sx = '';
                        $f = fopen('application/language/portuguese/find_lang.php','w');
                        fwrite($f,'<?php'.cr());
                        for ($r=0;$r < count($rlt);$r++)
                            {
                                $line = $rlt[$r];
                                $t1 = trim($line['msg_term']);
                                $t2 = trim($line['msg_label']);
                                $ln = 
                                    '$lang[\''.$t1."'] = '".
                                    $t2."'; ".cr();
                                $sx .= $ln.'<br>';
                                fwrite($f,$ln);        
                            }
                        fwrite($f,'?>'.cr());
                        fclose($f);
                        return($sx);
                    }
				if (strlen($CI->lang->line($t)) > 0)
					{
						return($CI->lang->line($t));
					} else {
					    $sql = "select * from msg where msg_term = '$t' ";
                        $rlt = $CI->db->query($sql);
                        $rlt = $rlt->result_array();
                        if (count($rlt) == 0)
                            {
                                $sql = "insert into msg
                                            (msg_term, msg_label)
                                            value
                                            ('$t','$t')";
                                $rlt = $CI->db->query($sql);
                            }
						return($t);
					}
			}
	}
?>
