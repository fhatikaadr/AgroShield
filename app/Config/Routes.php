<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->post('/api/sensor', 'Api::create');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/getdata', 'Dashboard::getData');
$routes->get('/chatbot', 'Dashboard::chatbot'); 
$routes->get('/analitik', 'Dashboard::analitik');
$routes->get('/analitik/getdata', 'Dashboard::getAnalitikData');
$routes->get('/weather', 'Dashboard::weather'); 

