<?php
namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();


helper(['boostrap','url','graphs','sisdoc_forms','form','nbr']);

define("PATH","index.php/find/");

class Find extends BaseController
	{

	public function __construct()
		{
		$this->Books = new \App\Models\Books();
		$this->Socials = new \App\Models\Socials();
		$this->FindSearch = new \App\Models\FindSearch();	
	
		helper(['boostrap','url','canvas']);
		define("LIBRARY", "1003");
		define("LIBRARY_NAME", "FIND");				
		}
	public function index()
	{
		$sx = '';
		$sx .= $this->cab();
		$sx .= $this->navbar();	

		$sx .= $this->FindSearch->banner();	
		$sx .= $this->FindSearch->search();

		$st = '<h2>'.lang('find.latest_acquisitions').'</h2>';
		$st .= $this->Books->latest_acquisitions();
		$sx .= bs($st);		

		return $sx;
	}

	private function cab($dt=array())
		{
			$title = 'Find - Library Open System';
			if (isset($dt['title'])) { $title = $dt['title']; } 
			$sx = '<!doctype html>'.cr();
			$sx .= '<html>'.cr();
			$sx .= '<head>'.cr();
			$sx .= '<title>'.$title.'</title>'.cr();
			$sx .= '  <meta charset="utf-8" />'.cr();
			$sx .= '  <link rel="apple-touch-icon" sizes="180x180" href="'.base_url('img/favicon.png').'" />'.cr();
			$sx .= '  <link rel="icon" type="image/png" sizes="32x32" href="'.base_url('img/favicon.png').'" />'.cr();
			$sx .= '  <link rel="icon" type="image/png" sizes="16x16" href="'.base_url('img/favicon.png').'" />'.cr();
			$sx .= '  <!-- CSS -->'.cr();
			$sx .= '  <link rel="stylesheet" href="'.base_url('/css/bootstrap.css').'" />'.cr();
			$sx .= '  <link rel="stylesheet" href="'.base_url('/css/style.css?v=0.0.4').'" />'.cr();
			$sx .= ' '.cr();
			$sx .= '  <!-- CSS -->'.cr();
			$sx .= '  <script src="'.base_url('/js/bootstrap.js?v=5.0.2').'"></script>'.cr();
			$sx .= '<style>
					@font-face {font-family: "Handel Gothic";
					src: url("'.base_url('css/fonts/HandelGothic/handel_gothic.eot').'"); /* IE9*/
					src: url("'.base_url('css/fonts/HandelGothic/handel_gothic.eot?#iefix').'") format("embedded-opentype"), /* IE6-IE8 */
					url("'.base_url('css/fonts/HandelGothic/handel_gothic.svg#Handel Gothic').'") format("svg"); /* iOS 4.1- */
					}
					@import url(\'https://fonts.googleapis.com/css2?family=Nunito:wght@200&family=Roboto:wght@100&display=swap\');
					</style>
					';

			$sx .= '</head>'.cr();

			if (get("debug") != '')
				{
					$sx .= '<style> div { border: 1px solid #000000;"> </style>';
				}			
			return $sx;
		}

	private function navbar($dt=array())	
		{
			$title = 'Find - Library Open System';
			if (isset($dt['title'])) { $title = $dt['title']; } 
			$sx = '<nav class="navbar navbar-expand-lg navbar-light ">'.cr();
			$sx .= '  <div class="container-fluid">'.cr();
			$sx .= '    <a class="navbar-brand" href="'.base_url(PATH).'">'.$title.'</a>'.cr();
			$sx .= '    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">'.cr();
			$sx .= '      <span class="navbar-toggler-icon"></span>'.cr();
			$sx .= '    </button>'.cr();
			$sx .= '    <div class="collapse navbar-collapse" id="navbarSupportedContent">'.cr();
			$sx .= '      <ul class="navbar-nav me-auto mb-2 mb-lg-0">'.cr();
			/*
			$sx .= '        <li class="nav-item">'.cr();
			$sx .= '          <a class="nav-link active" aria-current="page" href="#">Home</a>'.cr();
			$sx .= '        </li>'.cr();
			$sx .= '        <li class="nav-item">'.cr();
			$sx .= '          <a class="nav-link" href="#">Link</a>'.cr();
    		$sx .= '		</li>'.cr();
			*/
			$sx .= '        <li class="nav-item dropdown">'.cr();
			$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
			$sx .= '            '.lang('find.Home').cr();
			$sx .= '          </a>'.cr();
			$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
			$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'events/').'">'.lang('proceedings.Menu.events').'</a></li>'.cr();
			$sx .= '          </ul>'.cr();
			$sx .= '        </li>'.cr();

			$sx .= '        <li class="nav-item dropdown">'.cr();
			$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
			$sx .= '            '.lang('find.Index').cr();
			$sx .= '          </a>'.cr();
			$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
			$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'indexes/authors').'">'.lang('find.Indexes.Authors').'</a></li>'.cr();
			$sx .= '          </ul>'.cr();
			$sx .= '        </li>'.cr();			

			//if ($this->Socials->perfil("#ADM"))
			{
				$sx .= '        <li class="nav-item dropdown">'.cr();
				$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
				$sx .= '            '.lang('events.proceedings').cr();
				$sx .= '          </a>'.cr();
				$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'proceedings').'">'.lang('events.proceedings.row').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'rdf').'">'.lang('events.rdf.row').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'proceedings/gets').'">'.lang('events.proceedings.gets').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.base_url(PATH.'export/rdf').'">'.lang('events.export.rdf').'</a></li>'.cr();
				$sx .= '          </ul>'.cr();
				$sx .= '        </li>'.cr();
			}
			$sx .= '      </ul>'.cr();

			/*
			$sx .= '        <li class="nav-item">'.cr();
			$sx .= '          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>'.cr();
			$sx .= '        </li>'.cr();
			$sx .= '      </ul>'.cr();
			*/

			/*
			$sx .= '      <form class="d-flex">'.cr();
			$sx .= '        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">'.cr();
			$sx .= '        <button class="btn btn-outline-success" type="submit">Search</button>'.cr();
			$sx .= '      </form>'.cr();
			*/
			
			$sx .= $this->Socials->nav_user();

			$sx .= '    </div>'.cr();
			$sx .= '  </div>'.cr();
			$sx .= '</nav>'.cr();
			return $sx;
		}
	function v($id)
		{
			$RDF = new \App\Models\RDF();

			$sx = $this->cab();
			$sx .= $this->navbar();
			$sx .= $this->FindSearch->banner();	

			$dt = $RDF->le($id);

			$Books = new \App\Models\Books();
			$sx .= $Books->view($dt);

			return $sx;			
		}
	function export($d1='',$d2='',$d3='',$d4='')	
		{
			$RDF = new \App\Models\RDF();

			$sx = $this->cab();
			$sx .= $this->navbar();
			$sx .= $this->FindSearch->banner();	
			
			/* Export Command */
			$sx .= $RDF->export($d1,$d2);

			return $sx;
		}

}
