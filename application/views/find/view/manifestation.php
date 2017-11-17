<?php
$date = '';
$editora = '';
$local = '';
$isbn = '';
$edicao = '';
$localizacao = '';

$rlt = $manifestation;
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
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
            case 'hasClassificationCDU':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                $linka = '</a>';
                $localizacao = $link.'CDU'.$line['n_name'].$linka;                
                break;
            case 'hasClassificationCDD':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                $linka = '</a>';
                $localizacao = $link.'CDD'.$line['n_name'].$linka;                
                break;
            case 'dateOfPublication':
                if (strlen($date) > 0)
                    {
                        $date .= '; ';
                    }                
                $date .= '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                $date .= trim($line['n_name']);
                $date .= '</a>';
                break;
            case 'isPlaceOfPublication':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                if (strlen($local) > 0)
                    {
                        $local .= '; '.$link.trim($line['n_name']).'</a>';
                    } else {
                        $local .= ': '.$link.trim($line['n_name']).'</a>';
                    }                 
                break;
            case 'isPublisher':
                if (strlen($editora) > 0)
                    {
                        $editora .= '; ';
                    }
                $editora .= '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                $editora .= trim($line['n_name']);
                $editora .= '</a>';
                break;                
            case 'hasISBN':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                if (strlen($isbn) > 0)
                    {
                        $isbn .= '; ';
                    }
                $isbn = $link.trim($line['n_name']).'</a>';
                break;
            case 'isEdition':
                $link = '<a href="'.base_url('index.php/main/a/'.$line['d_r2']).'">';
                if (strlen($edicao) > 0)
                    {
                        $edicao .= ', ';
                    }
                $edicao .= $link.trim($line['n_name']).'</a>';
                break;                
            }
    }
    /* regras */
    if (strlen($local) == 0) { $local = ': Sem local';}
    
    if (strlen($localizacao) == 0) { $localizacao = ': Sem local';} else {
        $localizacao = msg('classification').'<br>'.$localizacao;
    }
?>
<!---------------- MANIFESTATION ------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('ManifestationLabel');?></tt>
        </div>        
        <div class="col-md-10">
            <?php
            if (strlen($edicao) > 0)
                {
                    echo $edicao. ' - ';
                }
            if (strlen($editora.$local) > 0)
                {
                    echo $editora.$local.', ';
                }
            if (strlen($date) > 0)
                {
                    echo $date;
                }
            if (strlen($edicao.$editora.$date) > 0)
                {
                    echo '. ';
                }
            if (strlen($isbn) > 0)
                {
                    echo $isbn.'.';
                }                
            ?>
        </div>
        <div class="col-md-1 text-left" style="border-left: 1px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo $localizacao;?></tt>
        </div>                
    </div>
</div>
<br>    