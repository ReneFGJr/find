<?php
$erro = get("err");
?>
<!--- LOGIN --->
<div class="container">
	<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		<div class="panel panel-info" >
			<div class="panel-heading">
				<div class="panel-title">
					Sign In
				</div>
				<!---
				<div style="float:right; font-size: 80%; position: relative; top:-10px">
					<a href="#">Forgot password?</a>
				</div>
				-->
			</div>

			<div style="padding-top:30px" class="panel-body" >

				<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

				<?php 
					$atr = array('class' => 'form-horizontal', 'id' => 'loginform', 'role'=>'form');
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
					
					<?php
					if (isset($erro) and (strlen($erro) > 0))
						{
							echo '
							<div class="bg-danger" style="padding: 10px;">'.msg($erro).'</div>';	
						}
					?>

					<div class="input-group">
						<div class="checkbox">
							<label>
								<input id="login-remember" type="checkbox" name="dd3" value="1">
								<?php echo msg('remember_me');?> </label>
						</div>
					</div>

					<div style="margin-top:10px" class="form-group">
						<!-- Button -->

						<div class="col-sm-12 controls">
							<input type="submit" id="btn-login" href="#" class="btn btn-success" value="<?php echo msg('login');?>">
							<a id="btn-fblogin" href="<?php echo base_url('index.php/social/session/facebook/'); ?>" class="btn btn-primary">Login with Facebook</a>
							<a id="btn-fblogin" href="<?php echo base_url('index.php/social/session/google/'); ?>" class="btn btn-primary">Login with Google+</a>

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

			</div>
		</div>
	</div>
</div>
