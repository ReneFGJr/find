<?php
$nome = '';
$alt = '';
$born = '';
$dead = '';
$notas = '';
$w = $work[0]['d_r1'];
$link = '<a href="'.base_url('index.php/main/a/'.$w).'">';
echo $link.'[ed]</a>';
for ($r=0;$r < count($work);$r++)
    {
        $line = $work[$r];
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
            case 'prefLabel':
                $link = '<a href="'.base_url('index.php/main/v/'.$line['id_d']).'">';
                $nome = $link.trim($line['n_name']).'</a>';
                break;
            case 'altLabel':
                if (strlen($alt) > 0)
                    {
                        $alt .= '; ';
                    }
                $alt = trim($line['n_name']);
                break;
            case 'sourceNote':
                if (strlen($notas) > 0)
                    {
                        $notas .= '<br>';
                    }
                    $notas .= $line['n_name'];
                break;                
            case 'hasBorn':
                $link = '<a href="'.base_url('index.php/main/v/'.$line['id_d']).'">';
                $born = $link.trim($line['n_name']);
                $born .= '</a>';
                break;
            case 'hasDie':
                $link = '<a href="'.base_url('index.php/main/v/'.$line['id_d']).'">';
                $dead = $link.trim($line['n_name']);
                $dead .= '</a>';
                break;                
            }
    }
    $dates = '';
    if (strlen($born.$dead) > 0)
        {
            if (strlen($born) > 0)
                {
                    $dates = ', '.$born.'-';
                } else {
                    $dates .= ', -';
                }
            $dates .= $dead;                
        }
?>
<!---------------- WORK --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Person');?></tt>
        </div>
        <div class="col-md-11">
            <font style="font-size: 200%"><?php echo $nome;?><?php echo $dates;?></font>
            <?php
            if (strlen($alt) > 0)
                {
                    echo '<br>'.msg('alternativeNames').': <i>'.$alt.'</i>';
                }
            ?>
        </div>
        <?php
            if (strlen($notas) > 0)
                {
                    echo '<div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">';
                    echo '</div>';
                    echo '<div class="col-md-11" style="margin-top: 15px; font-size: 80%;">';
                    echo '<b>Notas</b><br>';
                    echo $notas;
                    echo '</div>';
                }
        ?>        
    </div>
</div>
<br>