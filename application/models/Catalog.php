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
			$sx .= '<div class="col=md-2">';
			$sx .= '<a href="'.base_url(PATH.'catalog/authority').'">';
			$sx .= '<img src="'.base_url('img/icon/icone_authority.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-2">';
			$sx .= '<a href="'.base_url(PATH.'catalog/concept').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_concept.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-2">';
			$sx .= '<a href="'.base_url(PATH.'catalog/geoname').'">';
			$sx .= '<img src="'.base_url('img/icon/icon_geo.png').'" class="img-fluid" style="width: 100%;">';
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col=md-2">';
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