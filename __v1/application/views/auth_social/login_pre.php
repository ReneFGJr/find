<?php
$back = substr(date("s"),1,1);
$back = strzero($back,2);
$back = 'img/background/back_'.$back.'.jpg';
?>
<div>
	<img src="<?php echo base_url($back);?>" alt="" class="maximage"/>
	<img class="gradient" src="<?php echo base_url('/img/background/gradient.png');?>" alt=""/>
</div>
<script type="text/javascript" charset="utf-8">
$(function(){
	// Trigger maximage
	jQuery('#maximage').maximage();
});		
</script>
<style type="text/css">
body {
    overflow:hidden;
}
</style>