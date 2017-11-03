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
if (!function_exists(('msg')))
	{
		function msg($t)
			{
				$CI = &get_instance();
				if (strlen($CI->lang->line($t)) > 0)
					{
						return($CI->lang->line($t));
					} else {
						return($t);
					}
			}
	}
	
/* Login */
$lang['Username'] = 'Usuário';
$lang['Password'] = 'Senha';
$lang['Keep me Signed in'] = 'Manter-se conectado';
$lang['Sign In'] = 'Entrar';
$lang['SIGN IN'] = 'Acessar';
$lang['SIGN UP'] = 'Sobre';
$lang['Forgot Password'] = 'Esqueceu sua senha';

$lang['contato_T'] = 'Telefone';
$lang['contato_E'] = 'E-mail';
$lang['g_ingresso'] = 'Ano ingresso';
$lang['g_integralizacao'] = 'Integralização';
$lang['bt_search'] = 'Busca';
$lang['bt_clear'] = 'Limpar';
$lang['bt_new'] = 'Novo';

$lang['i_i1'] = 'Cred. Total';
$lang['i_i2'] = 'Cred. Obrigatórios';
$lang['i_i3'] = 'Cred. Eletivos';
$lang['i_i4'] = 'Cred. Complementares';
$lang['i_i5'] = 'TIM';
$lang['i_i6'] = 'I1:';
$lang['i_i7'] = 'I2:';
$lang['i_i8'] = 'I3:';
$lang['i_i9'] = 'I4:';
$lang['i_i10'] = 'I5:';
$lang['i_i11'] = 'I6:';
$lang['i_i12'] = 'Ult.Matricula';
$lang['i_i13'] = 'Cred. Matriculado';
$lang['i_i14'] = 'Cred. Integr.';
$lang['i_i15'] = 'Cred. FF';

$lang['q_situacao_0'] = 'Não respondido';
$lang['q_situacao_1'] = 'Finalizado';
$lang['q_situacao_9'] = 'Cancelado';

?>
