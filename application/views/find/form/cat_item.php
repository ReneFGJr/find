<?php
$dd1 = get("dd1");
$dd2 = get("dd2");
$dd3 = get("dd3");
$dd4 = get("dd4");
?>
<form method="post">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>ITEM</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #a050a0;">
            ITEM<br>
            <span style="font-size: 75%"><I>novo</I></span>                
        </div>
        <div class="col-md-11">
            <!---------------------------- numero tombo -------------------->
            <span>Número tombo</span><br>
            <Input type="text" class="form-control" name="dd1" value="<?php echo $dd1;?>">                
            
            <!---------------------------- local do item ------------------->
            <br>Local do Item
            <select name="dd3" class="form-control">
                
            <?php
                if (strlen($form) > 0)
                    {
                        echo '<option>::: Local do Item :::</option>';
                        for ($r=0;$r < count($form);$r++)
                            {
                                $line = $form[$r];
                                $chk = '';
                                if ($line['id_n'] == $dd3) { $chk = 'selected'; }
                                echo '<option value="'.$line['id_n'].'" '.$chk.'>'.$line['n_name'].'</option>'.cr();
                            }
                    } else {
                        for ($r=0;$r < count($form);$r++)
                            {
                                $line = $form[$r];
                                $chk = '';
                                if ($line['id_n'] == $dd3) { $chk = 'selected'; }
                                echo '<option value="'.$line['id_n'].'" '.$chk.'>'.$line['n_name'].'</option>'.cr();
                            }                        
                    }
            ?>
            </select>
            
            <!---------------------------- forma de aquisicao -------------->
            <br>Forma de aquisição
            <select name="dd4" class="form-control">
                <option>::: Forma de aquisição :::</option>
            <?php
                for ($r=0;$r < count($acqu);$r++)
                    {
                        $line = $acqu[$r];
                        $chk = '';
                        if ($line['id_n'] == $dd4) { $chk = 'selected'; }
                        echo '<option value="'.$line['id_n'].'" '.$chk.'>'.$line['n_name'].'</option>'.cr();
                    }
            ?>
            </select>            
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