<?php
$style = '';
$rowc = '';
if (isset($bg))
	{
		if (substr($bg,0,1) == '#')
			{
				$style = ' background-color: '.$bg.';';
			}
	}

if (strpos($content,'container') > 0)
	{
		$type = '1-none';
		echo $content;
		echo cr();
	} else {
			/********* ROW */
			if (strpos($content,'class="row"') > 0)
				{
					$row = '';
					$rowa = '';					
				} else {
					$row = '<div class="row">';
					$rowa = '</div>';
					$rowc = 'ROW: Yes';
				}
			echo '<!--- content--->' . cr();
			if (isset($fluid)) 
			{
				$type = '2-fluid';
				echo '<div class="container-fluid">', cr();
				echo $row.cr();
				echo $content;
				echo $rowa.cr();
				echo '</div>'.cr();
			} else {
				$type = '3-normal';
				echo '<div class="container">', cr();
				echo $row.cr();
				echo $content;
				echo $rowa.cr();
				echo '</div>'.cr();
			}
	}

	echo '===>'.$type.' '.$rowc;
	if (isset($_GET['debug']))
	{
		echo  '<style> div { border: 1px solid #000; } </style>';
	}
?>