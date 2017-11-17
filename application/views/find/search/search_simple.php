<?php
$dd1 = get("dd1");
?>
<form method="get">
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form>
			<div class="input-group">				
				<input name="dd1" type="text" class="form-control" placeholder="Busca..." aria-describedby="btnGroupAddon" style="border:1px solid #c0c0c0;" value="<?php echo $dd1;?>">
				&nbsp;
				<input name="action" type="submit" class="btn btn-danger bts " id="btnGroupAddon" value="Pesquisa">
			</div>
			</form>
		</div>
	</div>
</div>
</form>
<style>
    .bts
        {
            cursor: pointer;
        }
</style>
