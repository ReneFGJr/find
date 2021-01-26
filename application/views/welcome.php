<?php
$back = '';
$IMG = "img/background/library_".LIBRARY.'.jpg';
if (file_exists($IMG))
{
	$back = 'style="background-image: url(\''.base_url($IMG).'\'); height: 130px; background-size: 1000px;';
}
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" <?php echo $back;?> >
			<span class="big">&nbsp;</span>
		</div>
	</div>
</div>
<br/>
