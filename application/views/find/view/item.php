<?php
$nr = '';
$local = '';
$status = 'Disponível';

$rlt = $item;
$w = $id;
$link = '<a href="'.base_url('index.php/main/a/'.$id).'">';
echo $link.'[ed]</a>';

if (count($rlt) == 0)
    {
        return('');
    }
for ($r = 0; $r < count($rlt);$r++)
    {
        $line = $rlt[$r];
        //print_r($line);
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
            case 'hasIdRegister':              
                $nr .= trim($line['n_name']);
                break;
            case 'hasPlaceItem':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                if (strlen($local) > 0)
                    {
                        $local .= '; '.$link.trim($line['n_name']).'</a>';
                    } else {
                        $local .= $link.trim($line['n_name']).'</a>';
                    }                 
                break;
            case 'isItemSituation':
                $status .= trim($line['n_name']);
                break;                
            }
    }
    /* regras */
    if (strlen($local) == 0) { $local = 'Sem exemplar disponível';}
?>
<!---------------- ITEM ------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Item');?></tt>
        </div>        
        <div class="col-md-11">
            <?php
            if (strlen($nr) > 0)
                {
                    echo $nr. ' - ';
                }
            if (strlen($status) > 0)
                {
                    echo $status.'. ';
                }
            if (strlen($local) > 0)
                {
                    echo '<br>'.$local;
                }
            ?>
        </div>
    </div>
</div>
<br>    