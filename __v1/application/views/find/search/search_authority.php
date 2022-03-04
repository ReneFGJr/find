<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form id="search" method="post">
			<div class="input-group">				
				<input type="text" name="search" class="form-control" placeholder="Informe o nome ou parte do nome, ex: MARIA JOSE" aria-describedby="btnGroupAddon" style="border:1px solid #c0c0c0;">
				&nbsp;
				<span class="btn btn-danger bts" id="btnGroupAddon">Busca autoridade</span>
			</div>
			</form>
		</div>
	</div>
</div>
<script>
    jQuery("#btnGroupAddon").click(function() {
        jQuery("#search").submit();
    });
</script>
<style>
    .bts
        {
            cursor: pointer;
        }
</style>
