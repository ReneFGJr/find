<?php
class books extends CI_model
{
	function search()
		{
			$form = new form;
			$cp = array();
			array_push($cp,array('$H8','','',false,false));
			array_push($cp,array('$A','',msg('search_ISBN'),true,true));
			array_push($cp,array('$S50','','ISBN',true,true));
			array_push($cp,array('$B8','',msg('search'),false,true));
			$sx = $form->editar($cp,'');

			return($sx);
		}

	function locate($isbn)
		{
			$isbn = $this->isbn->format($isbn);
			$sx = $isbn;

			/* Google */
			$google = $this->google_api->book($isbn);
			$amazon = $this->amazon_api->book($isbn);
			echo '<pre>';
			print_r($google);
			echo '<hr>';
			print_r($amazon);
			echo '</pre>';
			return($sx);
		}
}
?>