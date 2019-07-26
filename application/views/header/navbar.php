<?php
$ac = array('', '', '', '', '', '', '', '', '', '', '', '', '');
if (!isset($pag)) { $pag = 0;
}
$ac[$pag] = 'active';
if (!isset($url)) {
    $url = PATH;
}
if (!isset($logo)) {
    $logo = 'img/logo-brapci_livros_mini.png';
}
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded fixed-top">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="<?php echo base_url(PATH); ?>"><img src="<?php echo base_url($logo); ?>" style="height: 30px;"></a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item active">
                <a class="nav-link" href="<?php echo base_url(PATH); ?>"><?php echo msg("home"); ?>
                <span class="sr-only">(current)</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url(PATH . 'bookshelf'); ?>"><?php echo msg("bookshelf"); ?></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url(PATH . 'about'); ?>">Sobre</a>
            </li>
            <li class="nav-item" style="margin-right: 40px;">
                <a class="nav-link" href="<?php echo base_url(PATH . 'contact'); ?>">Contato</a>
            </li>
            
            <form class="form-inline my-2 my-lg-0 navbar-toggler-center" method="get">
                <input class="form-control mr-sm-2" name="dd1" type="search" placeholder="Pesquisar" aria-label="Pesquisar">
                <input name="action" type="hidden" value="search">
                <button class="btn btn-outline-success my-2 my-sm-0" name="acao" type="submit">
                    Pesquisar
                </button>
            </form>            
            <?php echo $this -> socials -> menu_user(); ?>            
        </ul>
    </div>
</nav>
<div style="height: 100px"></div>
