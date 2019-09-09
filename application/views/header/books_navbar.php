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
			<?php if (file_exists('application/models/Loans.php')) { ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo msg('Loans');?> </a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				    <?php if (perfil("#ADM#OPE")) { ?>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'mod/loans/users'); ?>"><?php echo msg('load_users');?></a>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'mod/loans/books'); ?>"><?php echo msg("loan_book");?></a>
				    <?php } ?>				    
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'mod/loans/renove'); ?>"><?php echo msg('loan_renove');?></a>
				</div>
			</li>
			<?php } ?>						
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo msg('index');?> </a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'indice/author'); ?>"><?php echo msg("index_authority");?></a>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'indice/editor'); ?>"><?php echo msg('index_editor');?></a>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'indice/serie'); ?>"><?php echo msg('index_serie');?></a>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'indice/title'); ?>"><?php echo msg('index_title');?></a>
				</div>
			</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url(PATH.'about'); ?>">Sobre</a>
            </li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo base_url(PATH.'contact'); ?>">Contato</a>
			</li>
			<!------ catalog ---->
			<?php 
			if (perfil('#ADM')==1) 
			{ ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Catalogação </a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'authority'); ?>"><?php echo msg("authority");?></a>
				    <a class="dropdown-item" href="<?php echo base_url(PATH.'catalog_work'); ?>">Bibliográfico</a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'cutter'); ?>"><?php echo msg("cutter");?></a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'vocabulary'); ?>">Vocabulários controlados</a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'catalog'); ?>">Etiquetas</a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'labels'); ?>"><?php echo msg('menuLabels');?></a>
				</div>
			</li>
			<?php } ?>
			
			<!------ ADMIN CONFIG ---->
            <?php 
            if (perfil('#ADM')==1) 
            { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?php echo base_url('index.php/books/config'); ?>" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo msg('menu_config');?></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
					<a class="dropdown-item" href="<?php echo base_url(PATH.'config/updates'); ?>"><?php echo msg("menu_updates_check");?></a>                	
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'config/msg'); ?>"><?php echo msg("menu_msg");?></a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'config/forms'); ?>"><?php echo msg("menu_forms");?></a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'config/authority'); ?>"><?php echo msg("menu_authority");?></a>
                    <a class="dropdown-item" href="<?php echo base_url(PATH.'config/class'); ?>"><?php echo msg("menu_class");?></a>
                </div>
            </li>                
            <?php } ?>
            
			<!------ ADMIN CONFIG ---->
            <?php 
            if (perfil('#ADM')==1)
            { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?php echo base_url(PATH.'superadmin/'); ?>" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo msg('superadmin');?></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <?php
                        echo '<a class="dropdown-item" href="'.base_url(PATH.'superadmin/own/').'">'.msg("superadmin_own").'</a>';
                    ?>
                </div>
            </li>                
            <?php } ?>
                        
            
                <?php echo $this->socials->menu_user();?>
            </li>           
                       			
		</ul>
	</div>
</nav>
