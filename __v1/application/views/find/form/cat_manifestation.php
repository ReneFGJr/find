<?php
$dd1 = get("dd1");
$dd2 = get("dd2");
$dd3 = get("dd3");
?>
<form method="post">
	<div class="container">
		<div class="row">
            <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
                <tt style="font-size: 100%;"><?php echo msg('Manifestation'); ?></tt>
            </div>			
            <div class="col-md-10">
                <h6>Seleciona a expressão dessa obra</h6>
            </div>          
		</div>
        <!--- part II -->		
        <div class="row">
            <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">                
            </div>          
                <?php
                
                for ($r = 0; $r < count($form); $r++) {
                    $l = $form[$r];
                    echo '<hr>';
                    echo '<div class="col-md-1">';
                    echo '</div>';
                    
                    echo '<div class="col-md-2 btn btn-secondary">';
                    echo '<input type="radio" name="dd2" value="' . $l[0]['id'] . '"> ';
                    echo '<b>' . $l[0]['name'] . '</b>';
                    echo '<br><i>' . $l[1]['name'] . '</i>';
                    echo '</div>';
                }
                ?>
        </div>


		<div class="row">
		    
            <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
                
            </div>          
		    		
            <div class="col-md-10 text-right">
                <br>
                <div class="text-right">
                    <input type="submit" name="action" value="gravar >>>>" class="btn btn-primary">
                </div>				
           </div>
        </div>
    </div>
</form>