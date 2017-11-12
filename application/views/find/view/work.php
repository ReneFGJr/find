<?php
$title = '';
$subtitle = '';
$autor = '';
$tradutor = '';
$ilustrador = '';
$organizador = '';
$tipo = '';
$w = $work[0]['d_r1'];
$link = '<a href="'.base_url('index.php/main/a/'.$w).'">';
echo $link.'[ed]</a>';
for ($r=0;$r < count($work);$r++)
    {
        $line = $work[$r];
        $class = $line['c_class'];
        switch($class)
            {
            case 'hasTitle':
                $title = trim($line['n_name']);
                break;
            case 'hasSubtitle':
                $subtitle = ': '.trim($line['n_name']);
                break;
            case 'hasAuthor':
                if (strlen($autor) > 0)
                    {
                        $autor .= '; ';
                    }
                $autor .= '<a href="'.base_url('index.php/main/v/'.$line['d_r2']).'" style="color: #00008;">';
                $autor .= trim($line['n_name']);
                $autor .= '</a>';
                break; 
            case 'hasTranslator':
                if (strlen($tradutor) > 0)
                    {
                        $tradutor .= '; ';
                    }
                $tradutor .= '<a href="'.base_url('index.php/main/v/'.$line['d_r2']).'" style="color: #000080;">';
                $tradutor .= trim($line['n_name']);
                $tradutor .= '</a>';
                break;                                
            case 'hasFormWork':
                $tipo = trim($line['n_name']);
                break;
            }
    }
?>
<!---------------- WORK --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Work');?></tt>
        </div>
        <div class="col-md-11">
            <a href="<?php echo base_url('index.php/main/v/'.$w);?>">
            <span style="font-size: 140%; color: #000000;"><b><?php echo $title.$subtitle; ?></b></span>
            </a>
            <br>
            <i><?php echo '<b>'.$autor.'</b>';?></i>
            <?php
            if (strlen($tradutor) > 0)
                {
                    echo '. <i>'.msg('Translator').': <b>'.$tradutor.'</b></i>';
                }
            ?>
        </div>
    </div>
</div>
<br>