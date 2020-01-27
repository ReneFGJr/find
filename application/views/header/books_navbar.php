<?php
$ac = array('', '', '', '', '', '', '', '', '', '', '', '', '');
if (!isset($pag)) { $pag = 0;
}
$ac[$pag] = 'active';
if (!isset($url))
{
	$url = PATH;
}
if (!isset($logo))
{
	$logo = 'img/logo-brapci_livros_mini.png';
}
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="<?php echo base_url(PATH); ?>"><img src="<?php echo base_url($logo);?>" style="height: 30px;"></a>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">

			
			<li class="nav-item active">
				<a class="nav-link" href="<?php echo base_url(PATH); ?>">Catálogo <span class="sr-only">(current)</span></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'bookshelf'); ?>"><?php echo msg("bookshelf");?></span></a>
			</li>

			<?php
			if (perfil("#ADM") > 0)
			{ 
				echo '
				<li class="nav-item">
				<a class="nav-link" href="'.base_url(PATH.'setup').'">Configurações</a>
				</li>';
			}
			?>

			<?php
			if (perfil("#ADM#PPT") > 0)
			{ 
				echo '
				<li class="nav-item">
				<a class="nav-link" href="'.base_url(PATH.'preparation').'">Preparo Técnico</a>
				</li>';
			}
			?>

			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'about'); ?>">Sobre</a>
			</li>
		</ul>
	</div>
	<div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item ml-auto">
				<?php echo $this->socials->menu_user();?>
			</li> 
		</ul>
	</div>
</nav>
