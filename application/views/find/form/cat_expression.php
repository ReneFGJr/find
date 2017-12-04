<?php
$dd1 = get("dd1");
$dd2 = get("dd2");
$dd3 = get("dd3");
?>
<form method="post">
	<div class="container">
		<div class="row">

        <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Expression'); ?></tt>
        </div>			
			<div class="col-md-10">

				<br>
				Formato do trabalho
				<select name="dd2" class="form-control">
					<option value="">::: Formato da Expressão :::</option>
					<?php

                    for ($r = 0; $r < count($form); $r++) {
                        $line = $form[$r];
                        $chk = '';
                        if ($line['id_cc'] == $dd2) { $chk = 'selected';
                        }
                        echo '<option value="' . $line['id_cc'] . '" ' . $chk . '>' . $line['n_name'] . '</option>' . cr();
                    }
					?>
				</select>
				

                <br><br>
                Idioma da expressão
                <select name="dd3" class="form-control">
                    <option value="">::: Idioma da Expressão :::</option>
                    <?php

                    for ($r = 0; $r < count($linguage); $r++) {
                        $line = $linguage[$r];
                        $chk = '';
                        if ($line['id_cc'] == $dd3) { $chk = 'selected';
                        }
                        echo '<option value="' . $line['id_cc'] . '" ' . $chk . '>' . $line['n_name'] . '</option>' . cr();
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