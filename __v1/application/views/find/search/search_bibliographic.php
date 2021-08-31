<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form id="search" method="post">
			<div class="input-group">				
				<input type="text" name="search" class="form-control" placeholder="Informe o título do trabalho, ex: PLANEJAMENTO DE BIBLIOTECA" aria-describedby="btnGroupAddon" style="border:1px solid #c0c0c0;">
				&nbsp;
				<span class="btn btn-danger bts" id="btnGroupAddon">Busca trabalhos</span>
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
