			<form method="post">
			<h3 class="Roboto">Gerar nova senha</h3>	
			<input type="hidden" class="form-control" name="dd1"><br>	
			<span class="small">Informe nova senha</span><br>
			<input type="password" class="form-control" name="dd2"><br>	
			<span class="small">Confirme nova senha</span><br>
			<input type="password" class="form-control" name="dd3"><br>				
			<input type="submit" class="btn btn-primary" name="acao" value="alterar senha"><br>
			</form>			
			<?php if (isset($erro)) { echo $erro; } ?>
