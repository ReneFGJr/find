<?php
class catalog extends CI_model
	{
		function index()
			{
				$sx = '<div class="container">';
				$sx .= '<div class="row">';
				$sx .= '<div class="col=md-2">';
				$sx .= '<a href="'.base_url(PATH.'catalog/autority').'">';
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