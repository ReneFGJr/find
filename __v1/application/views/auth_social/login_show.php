<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h3>Perfis do usuário</h3>
			<table width="100%" class="table" border=0>
				<tr>
					<th width="150"></th>
					<th width="400"></th>
					<th></th>
				</tr>
				<tr valign="top">
					<td class="lt1" align="right"><?php echo msg("us_login"); ?></td>
					<td><?php echo $us_login; ?></td>
					
					<!---- Associar Perfirs ----->
					<td rowspan=20>
							<?php echo $us_perfil_list; ?>
							<br><br>
							<?php echo $us_perfil_associar; ?>
					</td>
				</tr>
			
				<tr>
					<td class="lt1" align="right"><?php echo msg("us_nome"); ?></td>
					<td class="lt3"><B><?php echo $us_nome; ?></B></td>
				</tr>
			
				<tr>
					<td class="lt1" align="right"><?php echo msg("us_badge"); ?></td>
					<td><?php echo($us_badge); ?></td>
				</tr>
			
				<tr>
					<td class="lt1" align="right"><?php echo msg("us_ultimo_acesso"); ?></td>
					<td><?php echo stodbr($us_last); ?> <?php echo substr($us_last, 10, 10); ?></td>
				</tr>
				
				<tr>
					<td colspan=2><hr size=1 width="50%"></td>
				</tr>		

			
			</table>
		</div>
	</div>
</div>
