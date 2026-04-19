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

$routes->get('/perfil', 'Perfil::index');

/*************** About */
$routes->group('about', function($routes) {
    $routes->get('us', 'PagesController::about');
    $routes->get('faq', 'PagesController::faq');
    $routes->get('contact', 'PagesController::contact');
    $routes->post('contact', 'PagesController::contact');
});


$routes->group('catalog', function($routes) {
    $routes->get('index', 'Catalog::index');
    $routes->get('catalogar', 'Catalog::catalogar');
    $routes->get('catalogar/isbn', 'Catalog::catalogar_isbn');
    $routes->post('catalogar/isbn', 'Catalog::catalogar_isbn');
    $routes->get('catalogar/phase/(:num)', 'Catalog::catalogar_phase/$1');
    $routes->get('catalogar/metadadoSearch/(:num)', 'Catalog::metadadoSearch/$1');
    $routes->post('catalogar/excluir', 'Catalog::excluir_exemplar');
    // Adicione outras rotas de catalogação aqui se necessário
});


$routes->group('admin', function($routes) {
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
