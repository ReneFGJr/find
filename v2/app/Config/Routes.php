<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'LibraryController::library');
$routes->get('/busca/resultado', 'LibraryController::buscaResultado');
$routes->get('/login', 'AuthController::login');
$routes->post('/authenticate', 'AuthController::authenticate');
$routes->get('/register', 'AuthController::register');
$routes->post('/store-user', 'AuthController::storeUser');
$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/send-password-reset', 'AuthController::sendPasswordReset');
$routes->get('/reset-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('/update-password', 'AuthController::updatePassword');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/bibliotecas', 'LibraryController::bibliotecas');
$routes->post('/bibliotecas/select', 'LibraryController::select');
$routes->get('/library', 'LibraryController::library');
$routes->get('/item/(:num)', 'LibraryController::item/$1');

$routes->get('/busca-avancada', 'BuscaAvancada::index');

$routes->get('/perfil', 'Perfil::index');

/*************** About */
$routes->group('about', function ($routes) {
    $routes->get('us', 'PagesController::about');
    $routes->get('faq', 'PagesController::faq');
    $routes->get('contact', 'PagesController::contact');
    $routes->post('contact', 'PagesController::contact');
});


$routes->group('catalog', function ($routes) {
    // Redirecionamento de /catalog/catalogar/phase para /catalog/catalogar
    $routes->get('catalogar/phase', 'Catalog::phaseRedirect');
    $routes->get('index', 'Catalog::index');
    $routes->get('form', function () {
        return redirect()->to('/catalog/index');
    });
    $routes->get('catalogar', 'Catalog::catalogar');
    $routes->get('catalogar/isbn', 'Catalog::catalogar_isbn');
    $routes->post('catalogar/isbn', 'Catalog::catalogar_isbn');
    $routes->get('catalogar/phase/(:num)', 'Catalog::catalogar_phase/$1');
    $routes->get('catalogar/metadadoSearch/(:num)', 'Catalog::metadadoSearch/$1');
    $routes->post('catalogar/metadadoSearch/(:num)', 'Catalog::metadadoSearch/$1');
    $routes->post('catalogar/excluir', 'Catalog::excluir_exemplar');

    $routes->get('catalogar/no_action/(:num)', 'Catalog::no_action/$1');

    $routes->group('rdf', function ($routes) {
        $routes->match(['post','get'], 'concept_add', 'Catalog\\RDF::rdf_concept_add');
        $routes->match(['post','get'], 'text_add', 'Catalog\\RDF::rdf_text_add');
        $routes->match(['post', 'get'], 'text_edit', 'Catalog\\RDF::rdf_text_edit');
    });

    // Nova rota para redirecionamento sem parâmetro
    $routes->get('catalogar/metadadoSearch', 'Catalog::metadadoSearchRedirect');
    // Adicione outras rotas de catalogação aqui se necessário
    $routes->get('check', 'Catalog::checkerModel');
    $routes->get('reindex', 'Catalog::reindexModel');
    $routes->get('rebuild_fields', 'Catalog::rebuildModel');

    // Cover
    $routes->match(['get', 'post'], 'upload_cover', 'Find\Rdf\Form::upload_cover');

    $routes->match(['get', 'post'], 'import_z3950', 'Catalog::inport_z3050');
    $routes->match(['get', 'post'], 'import_marc21', 'Catalog::import_marc21');

    // Catalogar Item //
    $routes->get('item/form', 'Catalog::form_item');

    // utilitários //
    $routes->get('util', 'Catalog::util');

    // Etiquetas //
    $routes->get('label', 'Find\\Item::etiquetas');
    $routes->post('label', 'Find\\Item::etiquetas');
    $routes->get('label/(:any)', 'Find\\Item::etiquetas/$1');
    $routes->get('status/(:num)', 'Find\\Item::change_status/$1');
    $routes->post('label/save', 'Find\\Item::etiquetas_save');

    // Autoridade (Autores e Tradutores)
    $routes->get('authority', 'Catalog\\Authority::index');
    $routes->get('authority/(:num)', 'Catalog\\Authority::edit/$1');
    $routes->post('authority/(:num)/save', 'Catalog\\Authority::save_remissive/$1');
    $routes->get('authority/form_remissive', 'Catalog\\Authority::form_remissive');
    $routes->post('authority/form_remissive', 'Catalog\\Authority::form_remissive');
});


$routes->group(
    'indexes',
    function ($routes) {
        $routes->get('(:any)', 'LibraryController::indexes/$1');
    });

$routes->group('rdf', function ($routes) {
    // Rota para exclusão de registro rdf_data
    $routes->post('form/excluir_rdf_data', 'Find\Rdf\Form::excluirRdfData');
    $routes->get('form/(:num)', 'Find\\Rdf\\Form::index/$1');
    $routes->get('form_edit', 'Find\\Rdf\\Form_edit::index');
    $routes->post('form/salvar', 'Find\\Rdf\\Form_edit::salvar');
    $routes->post('form/excluir', 'Find\\Rdf\\Form_edit::excluir');
    $routes->get('range_id', 'Find\\Rdf\\Range_id::index');
    $routes->post('form/salvar_range', 'Find\\Rdf\\Form::salvar_range');
    /******* Concept */
    $routes->post('concept/salvar_literal', 'Find\\Rdf\\Form::salvar_literal');
    $routes->match(['get', 'post'], 'concept/create_concept', 'Find\\Rdf\\Concept::create_concept');

    $routes->get('searchConcept', 'Find\\Rdf\\Autocomplete::searchConcept');
    // Nova rota para adicionar atributo ao conceito
    $routes->post('concept/adicionar_atributo', 'Find\\Rdf\\Concept::adicionar_atributo');
    $routes->post('concept/add_link_concept', 'Find\\Rdf\\Concept::add_link_concept');
    $routes->get('concept/add_link_concept', 'Find\\Rdf\\Concept::add_link_concept');
    // Adiciona literal RDF
    $routes->post('form/adicionar_literal', 'Find\\Rdf\\Form::adicionar_literal');
});

$routes->group('admin', function ($routes) {
    $routes->get('users', 'AdminController::users');
    $routes->get('user/edit/(:num)', 'AdminController::editUser/$1');
    $routes->post('user/edit/(:num)', 'AdminController::saveUser/$1');
    $routes->get('user/profile/(:num)', 'AdminController::userProfile/$1');

    $routes->get('configuration', 'AdminController::configuration');
    $routes->get('roles', 'AdminController::roles');
    $routes->post('roles/add-member', 'AdminController::addMember');
    $routes->post('roles/disable-member', 'AdminController::disableMember');
    $routes->get('users/search', 'AdminController::searchUsers');
    $routes->get('places', 'AdminController::places');
    $routes->post('places/create', 'AdminController::createPlace');
    $routes->post('places/update-name', 'AdminController::updatePlaceName');
    $routes->get('library', 'AdminController::library');
    $routes->post('library/save', 'AdminController::saveLibrary');

    $routes->get('logo', 'AdminController::logo');
    $routes->post('logo/upload', 'AdminController::uploadLogo');

    /* Status */
    $routes->get('status', 'AdminController::indexStatus');
    $routes->get('status/create', 'AdminController::createStatus');
    $routes->post('status/create', 'AdminController::createStatus');
    $routes->get('status/edit/(:num)', 'AdminController::editStatus/$1');
    $routes->post('status/edit/(:num)', 'AdminController::editStatus/$1');
    $routes->get('status/delete/(:num)', 'AdminController::deleteStatus/$1');

});

$routes->get('/api/', 'Api::index');
$routes->get('/api', 'Api::index');

$routes->get('/tt', 'Tt::index');
$routes->get('/tt/(:any)', 'Tt::index/$1');
$routes->get('/tt/(:any)/(:any)', 'Tt::index/$1/$2');
$routes->get('/tt/(:any)/(:any)/(:any)', 'Tt::index/$1/$2/$3');

$routes->post('/tt/(:any)', 'Tt::index/$1');
$routes->post('/tt/(:any)/(:any)', 'Tt::index/$1/$2');
$routes->post('/tt/(:any)/(:any)/(:any)', 'Tt::index/$1/$2/$3');


$routes->get('/api/(:any)', 'Api::index/$1');
$routes->post('/api/(:any)', 'Api::index/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
