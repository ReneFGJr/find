<table class="table table-bordered">
    <tr>
        <th>Property</th>
        <th>Value</th>
    </tr>
    <?php
    $xkey = '';
    foreach($data as $key => $line) {
        echo '<tr>';
        if ($key != $xkey) {
            echo '<td width="5%">'.$key.'</td>';
            $xkey = $key;
        } else {
            echo '<td width="5%" class="text-center">&nbsp;</td>';
        }

        echo '<td width="25%" class="text-end">'.$line['Property'].'</td>';
        echo '<td  width="70%">';
        echo $line['Caption'];
        //pre($line,false);
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>