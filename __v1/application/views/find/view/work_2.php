<?php
require ("work_process.php");
?>

<style>
    div {
        border: 5px solid #000000;
    }
</style>
<!------------------------------------------------------------>
<div class="row" style="margin-bottom: 20px;">

    <!--- Imagem --->
    <div class="col-lg-3 col-md-5 col-xs-4 col-sm-4">
        <?php echo $linkm; ?><img src="<?php echo base_url($cover); ?>" width="150" style="box-shadow: 5px 5px 8px #888888;">
        </a>
    </div>
    <!-- Titulo e autor -->
    <div class="col-lg-6 col-md-7 col-xs-8  col-sm-8">
        <!---- Autor ---->
        <b><span style="font-size: 140%; color: #000000;"> <?php echo $linkm . $title . $linka; ?></span></b>
        <br>
        <!---- Autor ---->
        <b><i><?php echo $author; ?></i>
        <br>
        </b>
        <!--- Expressao e idioma ---->
        <?php echo $form; ?>:
        <?php echo $language; ?>
        <br>
        <?php echo $manifestacao; ?>
    </div>
    <div class="col-lg-3 col-md-7 col-xs-8  col-sm-8">
        <?php echo $itens; ?>
    </div>
</div>
