<?php
$nome = '';
$alt = '';
$hid = '';
$born = '';
$dead = '';
$notas = '';
$w = $id;
$link = '<a href="'.base_url(PATH.'a/'.$w).'">';
//echo $link.'[ed]</a>';
$img = '<img src="' . base_url('img/icon/icone_author.jpg') . '" class="img-fluid">';
$cutter = ''; 
$lattes = '';
for ($r=0;$r < count($person);$r++)
    {
        $line = $person[$r];
        $class = trim($line['c_class']);
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
			case 'hasCutter':
				$cutter = $link.'<span class="btn-primary" style="padding: 2px 5px; border-radius: 5px;" title="Cutter">'.trim($line['n_name']).'</span></a> ';
				break;
            case 'prefLabel':
                $link = '<a href="'.base_url(PATH.'v/'.$id).'">';
                $nome = $link.trim($line['n_name']).'</a>';
                break;
            case 'hasLattes':
                $link = '<a href="'.trim($line['n_name']).'" target="_new">';
                $lattes_icone = '<img src="'.base_url('img/icon/icone_lattes.png').'" style="height: 32px;" title="Link para o Lattes">';
                $lattes = $link.$lattes_icone.'</a> ';
                break;                
            case 'altLabel':
                $alt .= '<li>'.trim($line['n_name']).'</li>';
                break;
            case 'hiddenLabel':
                if (strlen($hid) > 0)
                    {
                        $hid .= '; ';
                    }
                $hid .= trim($line['n_name']);
                break;                
            case 'sourceNote':
                if (strlen($notas) > 0)
                    {
                        $notas .= '<br>';
                    }
                    $notas .= $line['n_name'];
                break;                
            case 'hasBorn':
                $link = '<a href="'.base_url(PATH.'v/'.$line['id_d']).'">';
                $born = $link.trim($line['n_name']);
                $born .= '</a>';
                break;
            case 'hasDie':
                $link = '<a href="'.base_url(PATH.'v/'.$line['id_d']).'">';
                $dead = $link.trim($line['n_name']);
                $dead .= '</a>';
                break;  
            case 'hasCover' :
                $img = $this -> frbr -> mostra_imagem($line['d_r2']);
                break;
            case 'hasFace' :
                $img = $this -> frbr -> mostra_imagem($line['d_r2']);
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
$img = troca($img,'class="img-fluid"','class="img-fluid img-person"'); 

/********  Classe RDF **/
$rdf = new rdf;       

?>
<!---------------- WORK --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Person');?></tt>            
        </div>
        <div class="col-md-9">
            <font style="font-size: 200%"><?php echo $nome;?><?php echo $dates;?></font>
            <?php
            if (strlen($cc_origin) > 0)
                {
                    echo '<br><tt>'.$rdf->show_rdf($cc_origin).'</tt>';
                }
            
            if (strlen($alt.$hid.$cutter) > 0)
                {
                    echo '<table width="100%">';
                    echo '<tr valign="top"><td width="50%">';
                    if (strlen($hid) > 0)
                        {
                        echo msg('alternativeNames').': <ul>'.$alt.'</ul>';
                        }
                    echo '</td>';
                    echo '<td width="50%">';
                    echo '<div id="hden" style="display: none;">'.cr();
                    echo msg('hiddenNames').': <ul>'.$hid.'</ul>';
                    echo '</div>'.cr();
                    echo '</td>';
                    echo '</tr>';
					echo '<tr>';
					echo '<td>'.$lattes.$cutter.'</td>'.cr();
					echo '</tr>'.cr();
                    echo '</table>';
                }                
            ?>
        </div>
        <div class="col-md-2 text-center">
              <?php echo $img;?>
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