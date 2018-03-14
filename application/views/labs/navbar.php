<?php
$ac = array('', '', '', '', '', '', '', '', '', '', '', '', '');
if (!isset($pag)) { $pag = 0;
}
$ac[$pag] = 'active';
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="<?php echo base_url('index.php/main'); ?>"><img src="<?php echo base_url('img/logo_library.png');?>" style="height: 30px;"></a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo base_url('index.php/main'); ?>">Catálogo <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <?php echo msg('index');?> </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/indice/author'); ?>"><?php echo msg("index_authority");?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/indice/editor'); ?>"><?php echo msg('index_editor');?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/indice/serie'); ?>"><?php echo msg('index_serie');?></a>
                </div>
            </li>           
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('index.php/main/contact'); ?>">Contato</a>
            </li>
            <!------ catalog ---->
            <?php 
            if (perfil('#ADM')==1) 
            { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Catalogação </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/authority'); ?>"><?php echo msg("authority");?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/catalog'); ?>">Preparo técnico</a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/cutter'); ?>"><?php echo msg("cutter");?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/vocabulary'); ?>">Vocabulários controlados</a>
                </div>
            </li>
            <?php } ?>
            
            <!------ ADMIN CONFIG ---->
            <?php 
            if (perfil('#ADM')==1) 
            { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?php echo base_url('index.php/main/config'); ?>" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo msg('menu_config');?></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/config/msg'); ?>"><?php echo msg("menu_msg");?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/config/forms'); ?>"><?php echo msg("menu_forms");?></a>
                    <a class="dropdown-item" href="<?php echo base_url('index.php/main/config/authority'); ?>"><?php echo msg("menu_authority");?></a>
                </div>
            </li>                
            <?php } ?>
            <li class="nav-item navbar-toggler-right">
                <?php echo $this->socials->menu_user();?>
            </li>           
                                
        </ul>
    </div>
</nav>
