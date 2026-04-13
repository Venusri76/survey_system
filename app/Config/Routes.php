<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Home
$routes->get('/', 'Home::index');

// ── User / Public Routes ───────────────────────────────────────
$routes->get('survey/(:segment)',          'SurveyController::take/$1');
$routes->post('survey/(:segment)/submit',  'SurveyController::submit/$1');
$routes->get('survey/(:segment)/thankyou', 'SurveyController::thankyou/$1');

// ── Admin Auth Routes ──────────────────────────────────────────
$routes->get('admin/login',  'Admin\AuthController::loginForm');
$routes->post('admin/login', 'Admin\AuthController::login');
$routes->get('admin/logout', 'Admin\AuthController::logout');

// ── Admin Protected Routes ─────────────────────────────────────
$routes->group('admin', ['filter' => 'adminauth'], function ($routes) {
    $routes->get('dashboard',                       'Admin\DashboardController::index');
    $routes->post('surveys/upload',                 'Admin\DashboardController::upload');
    $routes->post('surveys/toggle/(:num)',          'Admin\DashboardController::toggle/$1');
    $routes->get('surveys/delete/(:num)',           'Admin\DashboardController::delete/$1');
    $routes->get('surveys/(:num)/results',          'Admin\ResultsController::index/$1');
    $routes->get('surveys/(:num)/results/download', 'Admin\ResultsController::download/$1');
});