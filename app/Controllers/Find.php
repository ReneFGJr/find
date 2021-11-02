<?php
namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();


helper(['boostrap','url','graphs','sisdoc_forms','form','nbr']);

define("PATH",$_SERVER['app.baseURL']."index.php/find/");
define("MODULE",'find');
define("URL",$_SERVER['app.baseURL']);
define('PREFIX','find.');

class Find extends BaseController
	{

	public function __construct()
		{
		$this->Books = new \App\Models\Book\Books();
		$this->Socials = new \App\Models\Socials();
		$this->FindSearch = new \App\Models\FindSearch();	
	
		define("LIBRARY", "1003");
		define("LIBRARY_NAME", "FIND");				
		}
		
	public function index()
	{
		$Classification = new \App\Models\Classification();

		$sx = '';
		$sx .= $this->cab();
		$sx .= $this->navbar();	

		$sx .= $this->FindSearch->banner();	
		$sx .= $this->FindSearch->search();


		$st = '<h2>'.lang('find.latest_acquisitions').'</h2>';
		$st .= $this->Books->latest_acquisitions();

		$sc = '<h2>'.lang('find.classifications').'</h2>';
		$sc .= $Classification->sections();
		$sx .= bs(bsc($sc,3).bsc($st,9));	


		$sx .= $this->footer();	

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
			$sx .= '  <link rel="apple-touch-icon" sizes="180x180" href="'.URL.('img/favicon.png').'" />'.cr();
			$sx .= '  <link rel="icon" type="image/png" sizes="32x32" href="'.URL.('img/favicon.png').'" />'.cr();
			$sx .= '  <link rel="icon" type="image/png" sizes="16x16" href="'.URL.('img/favicon.png').'" />'.cr();
			$sx .= '  <!-- CSS -->'.cr();
			$sx .= '  <link rel="stylesheet" href="'.URL.('/css/bootstrap.css').'" />'.cr();
			$sx .= '  <link rel="stylesheet" href="'.URL.('/css/style.css?v=0.0.6').'" />'.cr();
			$sx .= ' '.cr();
			$sx .= '  <!-- CSS -->'.cr();
			$sx .= '  <script src="'.URL.('/js/bootstrap.js?v=5.0.2').'"></script>'.cr();
			$sx .= '<style>
					@font-face {font-family: "Handel Gothic";
					src: url("'.URL.('css/fonts/HandelGothic/handel_gothic.eot').'"); /* IE9*/
					src: url("'.URL.('css/fonts/HandelGothic/handel_gothic.eot?#iefix').'") format("embedded-opentype"), /* IE6-IE8 */
					url("'.URL.('css/fonts/HandelGothic/handel_gothic.svg#Handel Gothic').'") format("svg"); /* iOS 4.1- */
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
			$Logo = new \App\Models\Library\Logos();
			$title = 'Find';
			if (isset($dt['title'])) { $title = $dt['title']; } 
			$sx = '<nav class="navbar navbar-expand-lg navbar-light ">'.cr();
			$sx .= '  <div class="container-fluid">'.cr();
			$sx .= '    <a class="navbar-brand" href="'.PATH.'">'.$Logo->logo_mini(LIBRARY).'</a>'.cr();
			$sx .= '    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">'.cr();
			$sx .= '      <span class="navbar-toggler-icon"></span>'.cr();
			$sx .= '    </button>'.cr();
			$sx .= '    <div class="collapse navbar-collapse" id="navbarSupportedContent">'.cr();
			$sx .= '      <ul class="navbar-nav me-auto mb-2 mb-lg-0">'.cr();
			$sx .= '        <li class="nav-item dropdown">'.cr();
			$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
			$sx .= '          '.lang('find.index').cr();
			$sx .= '          </a>'.cr();
			$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
			$sx .= '            <li><a class="dropdown-item" href="'.PATH.'events/'.'">'.lang('proceedings.Menu.events').'</a></li>'.cr();
			$sx .= '          </ul>'.cr();
			$sx .= '        </li>'.cr();

			$sx .= '
							<li class="nav-item">
								<a class="nav-link" href="'.PATH.'users/'.'">'.lang('find.users').'</a>
							</li>			
			';

			$sx .= '        <li class="nav-item dropdown">'.cr();
			$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
			$sx .= '            '.lang('find.Index').cr();
			$sx .= '          </a>'.cr();
			$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
			$sx .= '            <li><a class="dropdown-item" href="'.URL.(PATH.'indexes/authors').'">'.lang('find.Indexes.Authors').'</a></li>'.cr();
			$sx .= '          </ul>'.cr();
			$sx .= '        </li>'.cr();			

			//if ($this->Socials->perfil("#ADM"))
			{
				$sx .= '        <li class="nav-item dropdown">'.cr();
				$sx .= '          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.cr();
				$sx .= '            '.lang('events.proceedings').cr();
				$sx .= '          </a>'.cr();
				$sx .= '          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.URL.(PATH.'proceedings').'">'.lang('events.proceedings.row').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.URL.(PATH.'rdf').'">'.lang('events.rdf.row').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.URL.(PATH.'proceedings/gets').'">'.lang('events.proceedings.gets').'</a></li>'.cr();
				$sx .= '            <li><a class="dropdown-item" href="'.URL.(PATH.'export/rdf').'">'.lang('events.export.rdf').'</a></li>'.cr();
				$sx .= '          </ul>'.cr();
				$sx .= '        </li>'.cr();
			}
			$sx .= '      </ul>'.cr();
			$sx .= $this->Socials->nav_user();

			$sx .= '    </div>'.cr();
			$sx .= '  </div>'.cr();
			$sx .= '</nav>'.cr();
			return $sx;
		}
	private function footer()
		{
			$sx = '<!-- Footer -->
					<footer class="page-footer font-small blue-grey lighten-5" style="margin-top: 50px;">

					<div style="background-color: #21d192;">
						<div class="container">

						<!-- Grid row-->
						<div class="row py-4 d-flex align-items-center">

							<!-- Grid column -->
							<div class="col-md-6 col-lg-5 text-center text-md-left mb-4 mb-md-0">
							<h6 class="mb-0">Get connected with us on social networks!</h6>
							</div>
							<!-- Grid column -->

							<!-- Grid column -->
							<div class="col-md-6 col-lg-7 text-center text-md-right">

							<!-- Facebook -->
							<a class="fb-ic">
								<i class="fab fa-facebook-f white-text mr-4"> </i>
							</a>
							<!-- Twitter -->
							<a class="tw-ic">
								<i class="fab fa-twitter white-text mr-4"> </i>
							</a>
							<!-- Google +-->
							<a class="gplus-ic">
								<i class="fab fa-google-plus-g white-text mr-4"> </i>
							</a>
							<!--Linkedin -->
							<a class="li-ic">
								<i class="fab fa-linkedin-in white-text mr-4"> </i>
							</a>
							<!--Instagram-->
							<a class="ins-ic">
								<i class="fab fa-instagram white-text"> </i>
							</a>

							</div>
							<!-- Grid column -->

						</div>
						<!-- Grid row-->

						</div>
					</div>

					<!-- Footer Links -->
					<div class="container text-center text-md-left mt-5">

						<!-- Grid row -->
						<div class="row mt-3 dark-grey-text">

						<!-- Grid column -->
						<div class="col-md-3 col-lg-4 col-xl-3 mb-4">

							<!-- Content -->
							<h6 class="text-uppercase font-weight-bold">'.lang('social.COMPANY NAME').'</h6>
							<hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
							<p>Here you can use rows and columns to organize your footer content. Lorem ipsum dolor sit amet,
							consectetur
							adipisicing elit.</p>

						</div>
						<!-- Grid column -->

						<!-- Grid column -->
						<div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">

							<!-- Links -->
							<h6 class="text-uppercase font-weight-bold">'.lang('social.Products').'</h6>
							<hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
							<p>
							<a class="dark-grey-text" href="#!">MDBootstrap</a>
							</p>
							<p>
							<a class="dark-grey-text" href="#!">MDWordPress</a>
							</p>
							<p>
							<a class="dark-grey-text" href="#!">BrandFlow</a>
							</p>
							<p>
							<a class="dark-grey-text" href="#!">Bootstrap Angular</a>
							</p>

						</div>
						<!-- Grid column -->

						<!-- Grid column -->
						<div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">

							<!-- Links -->
							<h6 class="text-uppercase font-weight-bold">'.lang('social.Useful_links').'</h6>
							<hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
							<p>
							<a class="dark-grey-text" href="#!">'.lang('social.profile').'</a>
							</p>
							<p>
							<a class="dark-grey-text" href="#!">'.lang('social.help').'</a>
							</p>

						</div>
						<!-- Grid column -->

						<!-- Grid column -->
						<div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">

							<!-- Links -->
							<h6 class="text-uppercase font-weight-bold">'.lang('social.Contact').'</h6>
							<hr class="teal accent-3 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
							<p>
							<i class="fas fa-home mr-3"></i> New York, NY 10012, US</p>
							<p>
							<i class="fas fa-envelope mr-3"></i> info@example.com</p>
							<p>
							<i class="fas fa-phone mr-3"></i> + 01 234 567 88</p>
							<p>
							<i class="fas fa-print mr-3"></i> + 01 234 567 89</p>

						</div>
						<!-- Grid column -->

						</div>
						<!-- Grid row -->

					</div>
					<!-- Footer Links -->

					<!-- Copyright -->
					<div class="footer-copyright text-center text-black-50 py-3">© 2019-'.date("Y").' Copyright:
						<a class="dark-grey-text" href="https://github.com/ReneFGJr/find" target="_github">GitHub / ReneFGJr / Find </a>
					</div>
					<!-- Copyright -->

					</footer>
					<!-- Footer -->';
			return $sx;
		}

	function cover($isbn='0788598802633',$type='')
		{
			$IMG = new \App\Models\Images();
			if ($isbn == 'check')
				{
					$sx = $this->cab();
					$sx .= $this->navbar();
					$sx .= $this->FindSearch->banner();	

					$dir = '../_covers/image/';
					$sx .= bs(bsc($IMG->check($dir),12));
				} else {
					$sx = $IMG->resize('../_covers/image/'.$isbn.'.jpg','');
				}
			return $sx;
		}
		
	function item($id)
		{
			$sx = $this->cab();
			$sx .= $this->navbar();
			$sx .= $this->FindSearch->banner();	

			$Books = new \App\Models\Book\Books();
			$sx .= $Books->view_item($id);

			return $sx;				
		}
	function v($id)
		{
			$sx = $this->cab();
			$sx .= $this->navbar();
			$sx .= $this->FindSearch->banner();	

			$Books = new \App\Models\Book\Books();
			$sx .= $Books->v($id);

			return $sx;			
		}

	function users($d1='',$d2='',$d3='',$d4='')	
		{
			$User = new \App\Models\User();

			$sx = $this->cab();
			$sx .= $this->navbar();
			$sx .= $this->FindSearch->banner();	
			
			/* Export Command */
			$sx .= $User->index($d1,$d2,$d3,$d4);

			$sx .= $this->footer();

			return $sx;
		}

	function config($d1='',$d2='')
		{
			$tela = $this->cab();
			$tela .= $this->navbar();
			$tela .= $this->FindSearch->banner();

			$Libraries = new \App\Models\Library\Libraries();
			$tela .= $Libraries->index($d1,$d2);

			$tela .= $this->footer();
			return $tela;
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

