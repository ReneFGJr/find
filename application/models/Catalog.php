<?php
/*
VIAF API = https://platform.worldcat.org/api-explorer/apis/VIAF
*/
class catalog extends CI_model
{
	function authority()
	{
		$sx = '';
		$sx .= $this -> load -> view('find/authority',null,true);
		$sx .= $this -> load -> view('find/search/search_authority', null,true);
		$sx .= $this->viaf();
		return($sx);
	}

	function book($act='')
	{
		$this->load->model("covers");
		$this->load->model("books");
		$this->load->model("isbn");
		$this->load->model("languages");
		$this->load->model("generes");
		$this->load->model("authors");
		$this->load->model("google_api");
		$this->load->model("amazon_api");
		$this->load->model("oclc_api");
		$this->load->model("marc_api");

		
		$sx = '<div class="row">';
		$sx .= '<div class="col-md-2"><img src="'.base_url('img/others/isbn.png').'" class="img-fluid"></div>';
		$sx .= '<div class="col-md-10">'.msg('search_isbn').'</div>';
		$sx .= '<div class="col-md-12">'.$this -> books->search().'</div>';
		$sx .= '</div>';

		/***************************************************************************/
		$sx .= $this->marc_api->form();

		$isbn = get("dd2");
		if (strlen($isbn) > 0)
			{
				$sx .= $this->books->locate($isbn);
			}

		$marc = get("dd6");
		if (strlen($marc) > 0)
			{
				$sx .= $this->books->marc_import($marc);
			}			
		
		return($sx);
	}	

	function viaf()
	{
		/***************/
		$sx = '';
		$sx .= '<div class="row" style="margin-top: 30px;">' . cr();
		$sx .= '      <div class="col-md-2">';
		$sx .= '          <a href="https://viaf.org/" target="_new_viaf_' . date("dhs") . '" class="btn btn-secondary">
		<img src="' . base_url('img/logo/logo_viaf.jpg') . '" class="img-fluid"></a>' . cr();
		$sx .= '      </div>' . cr();
		$sx .= '      <div class="col-md-10">' . cr();
		$sx .= msg('find_viaf');
		$sx .= '          <form method="post" action="' . base_url(PATH . "authority/") . '">' . cr();
		$sx .= '          ' . cr();
		$sx .= '          <div class="input-group">
		<input type="text" name="ulr_viaf" id="viafid" value="" class="form-control">
		<input type="hidden" name="action" value="viaf_inport">
		<span class="input-group-btn">
		<input type="submit" name="acao"  class="btn btn-danger" value="' . msg('inport') . '">
		</span>
		</div>';
		$sx .= '          </form>' . cr();
		$sx .= '          <span class="small">Ex: https://viaf.org/viaf/122976/#Souza,_Herbert_de</span>';
		$sx .= '      </div>' . cr();
		$sx .= '  </div>' . cr();

		$sx .= '
		<script>

		$( function() {
			var cache = {};
			$( "#viafid" ).autocomplete(
			{
				minLength: 2,
				source: function( request, response ) {
					var term = request.term;
					if ( term in cache ) {
						response( cache[ term ] );
						return;
					}

					$.getJSON( "'.base_url(PATH.'ajax/viaf_autocomplete').'", request, function( data, status, xhr ) {
						cache[ term ] = data;
						response( data );
						});
					}
				}
				);
			}
			);
			</script>';
			return($sx);			
		}

		function index($path='',$id='')
		{
			switch($path)
			{
				case 'book':
				$sx = $this->book();
				break;

				case 'authority':
				$sx = $this->authority();
				break;

				/************************************************************ default ***/
				default:
				$sx = $this->menu();
				break;
			}
			return($sx);
		}
		function menu()
		{
			$sx = '<div class="container">';
			$sx .= '<div class="row">';

			$sx .= '<div class="col=md-1">';
			$sx .= '<a href="'.base_url(PATH.'catalog/book').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_book.png').'" class="img-fluid" >';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-1">';
			$sx .= '<a href="'.base_url(PATH.'catalog/authority').'">';
			$sx .= '<img src="'.base_url('img/icon/icone_authority.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-1">';
			$sx .= '<a href="'.base_url(PATH.'catalog/concept').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_concept.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-1">';
			$sx .= '<a href="'.base_url(PATH.'catalog/geoname').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_geo.png').'" class="img-fluid">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-1">';
			$sx .= '<a href="'.base_url(PATH.'catalog/editora').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_editora.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '</div>';
			$sx .= '</div>';
			return($sx);
		}
	}
	?>