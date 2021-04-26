<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Pages');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


//Pages route
$routes->get('/', 'Pages::index');

$routes->match(['get', 'post'], 'login', 'Pages::login');
$routes->match(['get', 'post'], 'register', 'Pages::register');
$routes->match(['get', 'post'], 'emailverify/(:num)', 'Pages::emailverify/$1');
$routes->match(['get', 'post'], 'resend_vcode/(:num)', 'Pages::resend_vcode/$1');
$routes->get('chats', 'Pages::chats');
$routes->get('contact', 'Pages::contact');

$routes->get('chats', 'User::chats');
//AJAX chat request;
$routes->get('chatlistreload', 'User::chatlist_reload');
$routes->get('chatsearch', 'User::chat_search');
$routes->get('messagereload', 'User::message_reload');
$routes->get('outgoingdelete', 'User::outgoingdelete');
$routes->get('pusher', 'User::pushernoti');


$routes->get('friends', 'User::friends');
$routes->get('profile', 'User::profile');
$routes->get('edit', 'User::edit');

$routes->get('settings', 'Settings::settings');

$routes->get('logout', 'User::logout');

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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
