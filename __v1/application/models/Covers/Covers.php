<?php
class covers extends CI_model
{
	function btn_seek_cover($isbn)
	{
		$link = 'https://www.google.com/search?q='.$isbn.'+ISBN&tbm=isch';
		$sx = '<a href="'.$link.'" target="_blank">GOOGLE</a>';
		return($sx);
	}

	function btn_upload($isbn)
	{
		$link = '';
		$sx = '<a href="#" type="button" onclick="newwin(\''.base_url(PATH.'/ajax/cover_upload/'.$isbn).'\',600,400);">UPLOAD</a>';
		$sx .= '</a>'.cr();

		//$sx .= $this->cover_upload_html();

		return($sx);
	}

	function btn_upload_link($isbn)
	{
		$link = '';
		$sx = cr();
		$sx .= '<a href="#" type="button" data-toggle="modal" data-target="#exampleModal">LINK</a>'.cr();
		$sx .= '<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body" id="ajax_body">
		';
		$sx .= '<h4>ISBN: '.$isbn.'</h4>';
		$sx .= $this->cover_link_html();
		$sx .= '</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">'.msg('close').'</button>
		<button type="button" id="btn_submit_link" class="btn btn-primary">'.msg('send_file').'</button>
		</div>
		</div>
		</div>
		</div>';

		$sx .= '
		<script>
		$("#btn_submit_link").click(function () { enviarconsulta(); }); 
		$("#btn_submit_link").keypress(function (e) 
		{ 
			if (e.which == 13) { enviarconsulta(); } 
		}
		); 

		function enviarconsulta() 
		{ 
			var x=document.getElementById(\'cover_link\').value;

			jQuery.ajax(
			{
				type: "POST",
				dataType: "html",
				data: {"url" : x },
				url: "'.base_url(PATH.'ajax/cover_link/'.$isbn).'",
				crossDomain: true,
				success: function( html ) 
				{
					jQuery("#ajax_body").html(html);
				} 
			}
			);
		}
		</script>'.cr();
		return($sx);
	}

	function img($isbn)
	{
		$file = $this->img_name($isbn);
		if (file_exists($file))
		{
			$img = base_url($file);
		} else {
			$img = base_url('img/no_cover.png');
		}
		return($img);
	}
	function img_name($isbn)
	{
		$file = '_covers';
		check_dir($file);
		$file .= '/image';
		check_dir($file);
		$file .= '/'.trim($isbn).'.jpg';
		return($file);
	}

	function cover_upload_html($isbn='')
	{
		$sx = '';
		$sx .= '<form enctype="multipart/form-data" action="'.
				base_url(PATH.'ajax/cover_upload/'.$isbn).
				'" method="POST">
		<!-- MAX_FILE_SIZE deve preceder o campo input -->
		<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
		<!-- O Nome do elemento input determina o nome da array $_FILES -->
		Enviar esse arquivo: <input name="userfile" type="file" />
		<input type="submit" value="Enviar arquivo" />
		</form>';
		return($sx);
	}

	function cover_link_html()
	{
		$sx = 'Informe o link da Capa<br>
		<input type="text" name="cover_link" id="cover_link" class="small" style="width: 100%;"/>
		<br/>ex: http://www.ufrgs.br/ufrgs/logo.jpg'.cr();		
		return($sx);
	}

	function ajax_cover_upload($isbn)
	{
		$idm = 0;
		$this->load->model("books");
		$ok = -1;
		print_r($_FILES);
		if (isset($_FILES['userfile']))
		{
			$url = $_FILES['userfile']['tmp_name'];
			if (file_exists($url))
			{
				$ok  = 1;
			}
		} else {
			echo '<h1>'.msg('Cover_upload').'</h1>';
			echo $this->cover_upload_html($isbn);
			exit;
		}

		if ($ok == 1)
		{			
			$isbn = sonumero($isbn);
			$this->save($url,$isbn);
			echo wclose();
		} else {
			if ($idm == 0)
			{
			echo $this->cover_upload_html($isbn);
			echo message("ERRO: ISBN '.$isbn.' inválido ou não localizado na base",3);			
			}
		}		

	}

	function ajax_update($isbn,$url)
	{
		$this->load->model("books");

		if (substr($url,0,4) == 'http')
		{
			$idm = $this->books->recover_id_for_isbn($isbn);
			if ($idm > 0)
			{			
				$this->save($url,$isbn);
				echo refresh();
			} else {
				echo $this->cover_link_html();
				echo message("ERRO: ISBN '.$isbn.' inválido ou não localizado na base",3);						
			}
		} else {
			echo $this->cover_link_html();
			echo message("ERRO: Formato inválido, utilize uma localização da internet http:// ou https://",3);			
			echo form_focus("cover_link");
		}
	}

	function save($url,$isbn)
	{
		if (strlen($isbn) < 10)
		{
			echo 'ERRO NO NOME';
			echo 'RST '.$isbn;
			exit;
		}
		$img = $this->img_name($isbn);
		if (strlen($url) > 0)
		{
			$t = file_get_contents($url);
			if (strlen($t) > 0)
			{
				file_put_contents($img, $t);
			}
		}
		return(0);
	}
}
?>