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
		$sx = '<a href="#" type="button" data-toggle="modal" data-target="#uploadModal">UPLOAD</a>';
		$sx .= '</a>'.cr();
		$sx .= '
		<!-- Modal -->
		<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="uploadModalLabel">Upload Cover</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body" id="ajax_upload_body">		';
		$sx .= $this->cover_upload_html();
		$sx .= '</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">'.msg('close').'</button>
		<button type="button" id="btn_submit_upload" class="btn btn-primary">'.msg('send_file').'</button>
		</div>
		</div>
		</div>
		</div>';

		$sx .= '
		<script>
		$("#btn_submit_upload").click(function () { enviarconsulta_upload(); }); 

		function enviarconsulta_upload() 
		{ 
			var file_data = $("#sortpicture").prop("files")[0];   
			var form_data = new FormData();
			form_data.append("file", file_data);
			$.ajax(
			{
				url: "'.base_url(PATH.'/ajax/cover_upload/'.$isbn).'",
				dataType: "script",
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,
				type: "post",
				success: function(data){ 
					jQuery("#ajax_upload_body").html(data);
				}
			}
			);
		}
		</script>';

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

	function cover_upload_html()
	{
		$sx = '';
		$sx .= '<input id="sortpicture" type="file" name="sortpic" />';
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
		$this->load->model("books");

		$ok = -1;
		if (isset($_FILES['file']))
		{
			$url = $_FILES['file']['tmp_name'];
			if (file_exists($url))
			{
				$ok  = 1;
			}
		} else {
			echo $this->cover_upload_html();
			echo message("ERRO: Arquivo inválido ou não localizado na base",3);			
		}
		
		if ($ok == 1)
		{			
			$this->save($url,$isbn);
			echo refresh();
		} else {
			if ($idm == 0)
			{
			echo $this->cover_upload_html();
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
			$t = read_link($url);
			if (strlen($t) > 0)
			{
				file_put_contents($img, $t);
			}
		}
		return(0);
	}
}
?>