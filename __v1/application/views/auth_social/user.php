<?php
$pict = 'img/picture/photo-' . $us_badge . '.jpg';
if (!file_exists($pict)) {
	$picture = base_url('img/no_image.png');
} else {
	$picture = base_url($pict);
}
?>

	<div class="row">

		<div class="col-md-10">	
			<font class="small">nome</font><br>
			<font class="big"><?php echo $us_nome; ?></font>	
			
			<div class="row">
			<div class="col-md-5">
				<font class="small">e-mail</font><br>
				<font class="middle"><?php echo $us_email; ?></font>
			</div>
			
			<div class="col-md-5">
				<font class="small">login</font><br>
				<font class="middle"><?php echo $us_badge; ?></font>			
			</div>
			</div>
		</div>
		<div class="col-md-2">
			<img id="profile-picture" class="img-fluid" alt="" src="<?php echo $picture; ?>">
		</div>		
	</div>