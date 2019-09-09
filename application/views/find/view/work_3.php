
<?php
require ("work_process.php");
?>
<!------------------------------------------------------------>
<div class="row">
    <!--- Imagem --->
    <div class="col-lg-3 col-md-5 col-xs-4 col-sm-4">
        <?php echo $linkm; ?>
            <img src="<?php echo base_url($cover); ?>" class="img-fluid img-thumbnail" style="width: 100%;">
        </a>
    </div>
    
    <!-- Dados da Obra -->
    <div class="col-lg-6 col-md-7 col-xs-8  col-sm-8">
        <!---- Autor ---->
        <span class="work_title"><?php echo $linkm . $title . $linka; ?></span>
        <br>
        <!---- Autor ---->
        <?php if (strlen($author) > 0) { ?>
        <span class="work_author"><?php echo $author; ?></span>
        <br>
        <?php } ?>
        <!-- avaliations -->
        <?php echo $this->avaliations->show(1);?>
        <br>
        <br>
        
        <?php echo $this->loans->loan(1);?> 
        <?php echo $this->avaliations->heart(1,312);?>
        <br>
        <br>
        
        <!--- Expressao e idioma ---->
        <?php echo $form; ?>:
        <?php echo $language; ?>
        <br>
        <?php echo $manifestacao; ?>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>xx
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>xx
        
    </div>
    <div class="col-lg-3 col-md-7 col-xs-8  col-sm-8">
        <?php echo $itens; ?>
    </div>
</div>
