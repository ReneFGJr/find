<?php
if (!isset($url))
{
	$url = PATH;
}
$logo = $this->libraries->logo(0,-1);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
	<a class="navbar-brand" href="<?php echo base_url(PATH); ?>"><img src="<?php echo $logo;?>" style="height: 30px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH); ?>">Catálogo</a>
			</li>
			<?php
			/**************************************************** Índices */
				echo $this->libraries->library_index();
			?>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'bookshelf'); ?>"><?php echo msg("bookshelf");?></span></a>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'library/list'); ?>"><?php echo msg('library_list');?></a>
			</li>

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

			<?php if (perfil("#ADM#CGU#BRE") > 0) { ?>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'mod/loans/row'); ?>"><?php echo msg('Users');?></a>
			</li>
			<?php } ?>		

			<?php if (perfil("#ADM") > 0) { ?>
				<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<?php echo msg('Admin');?>
				</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
					<li><a class="dropdown-item" href="<?php echo base_url(PATH.'reports'); ?>"><?php echo msg('report');?></a></li>
					<li><a class="dropdown-item" href="<?php echo base_url(PATH.'setup'); ?>"><?php echo msg('setup');?></a></li>
					<li><a class="dropdown-item" href="<?php echo base_url(PATH.'social/group'); ?>"><?php echo msg('group');?></a></li>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="#">Something else here</a></li>
				</ul>
				</li>

				<li class="nav-item">
					<a class="nav-link" href="<?php echo base_url(PATH.'admin'); ?>">Admin</a>
				</li>
			<?php } ?>

			<?php 
                $socials = new socials;
                echo $socials -> menu_user(); 
                ?>
	      </ul>
      <form class="d-flex" action="<?php echo base_url(PATH);?>">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="<?php echo msg('Search');?>" name="dd1">
        <button class="btn btn-outline-success" type="submit"><?php echo msg('Search');?></button>
      </form>
    </div>
  </div>
</nav>
