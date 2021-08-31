<?php
$erro = get("err");
?>
<!--- LOGIN --->
<div class="container-fluid" style="background-color: #1d4161; opacity: 0.7; position: absolute; top: 20%; z-index: 999999; width: 100%;">
	<div id="loginbox" style="margin-top:15px;" class="col-md-3 col-sm-2 text-right">
		<img src="<?php echo base_url('img/logo/logo_png_bw.png');?>">
	</div>
	<div id="loginbox" style="margin-top:15px;" class="col-md-3 col-sm-4">

		<?php
		$atr = array('class' => 'form-horizontal', 'id' => 'loginform', 'role' => 'form');
		echo form_open(base_url('index.php/social/login_local'));
		?>
		<div style="margin-bottom: 25px" class="input-group">
			<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			<input id="login-username" type="text" class="form-control" name="dd1" value="" placeholder="username or email">
		</div>

		<div style="margin-bottom: 25px" class="input-group">
			<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			<input id="login-password" type="password" class="form-control" name="dd2" placeholder="password">
		</div>
	</div>
	<div style="margin-top:15px;" class="col-md-3 col-sm-3">
		<input type="submit" id="btn-login" href="#" class="btn btn-success" value="<?php echo msg('login'); ?>">
		<?php
		if (isset($erro) and (strlen($erro) > 0)) {
			echo '<div style="color:white; padding: 10px;">' . msg($erro) . '</div>';
		}
		?>
	</div>
	<div style="margin-top:15px;" class="col-md-2 col-sm-2 text-right">
		<div class="col-sm-12 controls">
		</div>
	</div>
	<!--
	<div class="form-group">
	<div class="col-md-12 control">
	<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
	Don't have an account!
	<a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()"> Sign Up Here </a>
	</div>
	</div>
	</div>
	-->
	</form>
	<a href="http://10.1.1.123:8080"><img src="<?php echo base_url('img/img_old_system.png');?>" align="right" height="60" title="sistema antigo"></a>	
</div>



