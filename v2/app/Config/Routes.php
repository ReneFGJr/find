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

/******************* User */
$routes->get('/login', 'AuthController::login');
$routes->post('/authenticate', 'AuthController::authenticate');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/send-password-reset', 'AuthController::sendPasswordReset');
$routes->get('/register', 'AuthController::register');
$routes->post('/store-user', 'AuthController::storeUser');

$routes->get('/reset-password/(:any)', 'AuthController::resetPassword/$1'); // Página para redefinir senha com token
$routes->post('/update-password', 'AuthController::updatePassword'); // Submissão do formulário de redefinição

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
$routes->get('/', 'Home::index');
$routes->get('/api/', 'Api::index');
$routes->get('/api', 'Api::index');

$routes->get('/api/(:any)', 'Api::index/$1');
$routes->post('/api/(:any)', 'Api::index/$1');

$routes->get('/tt', 'Tt::index');
$routes->get('/dashboard', 'Tt::index');


/****************************************** BiblioFind */
$routes->get('/bibliofind', 'BiblioFind::index');
$routes->get('/bibliofind/buscar', 'BiblioFind::buscar');
$routes->get('/bibliofind/reindex', 'BiblioFind::reindex');
$routes->get('/bibliofind/zerar', 'BiblioFind::zerar');
$routes->get('/bibliofind/marc21', 'BiblioFind::marc21');
$routes->post('/bibliofind/marc21', 'BiblioFind::marc21');
$routes->get('/bibliofind/v/(:any)', 'BiblioFind::v/$1');



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
