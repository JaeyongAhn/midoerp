<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login');
$routes->post('/auth/do_login', 'Auth::do_login');
$routes->post('/account/do_upload', 'Account::do_upload');
$routes->get('/account', 'Account::index');
$routes->get('/account/load_tags', 'Account::load_tags');
$routes->post('/account/save_tags', 'Account::save_tags');
$routes->post('/account/save_tag', 'Account::save_tag');
$routes->post('/account/search', 'Account::search');