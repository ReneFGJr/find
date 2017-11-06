<?php
$dd1 = get("dd1");
$dd2 = get("dd2");

?>
<form method="post">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>WORK</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #a050a0; font-size: 150%;">
            <tt>W<br>O<br>R<br>K</tt>                
        </div>
        <div class="col-md-11">
            <span>Título</span><br>
            <Input type="text" class="form-control" name="dd1" value="<?php echo $dd1;?>">
            <span>Sub-título</span><br>
            <Input type="text" class="form-control" name="dd2" value="<?php echo $dd2;?>">
            <br>
            <div class="text-right">
                <input type="submit" name="action" value="gravar >>>>" class="btn btn-primary">
            </div>
        </div>
    </div>
</div>
</form>
<style>
    .form-control
        {
            border: 1px solid #333333;
        }
</style>