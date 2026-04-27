<?php

use App\Controllers\HomeController;
use App\Controllers\CourseController2;
// use App\Controllers\CourseController;
// use App\Controllers\LegalController;
// use App\Controllers\HelpController;
// use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/',             [HomeController::class, 'index'], ['as' => 'home']);
// // Course routes
// $routes->get('/courses', [CourseController::class, 'index'], ['as' => 'courses']);
// // $routes->get('/courses/(:segment)', [CourseController::class, 'index'], ['as' => 'courses']);
// $routes->get('/courses/(:num)/continue', [CourseController::class, 'continue/$1'], ['as' => 'courses.continue']);
// $routes->get('/courses/(:num)/module/(:segment)', [CourseController::class, 'module_course/$1/$2'], ['as' => 'courses.module']);
// // $routes->get('/courses/module/(:segment)', 'CourseController::module/$1');

$routes->get('/course', [CourseController2::class, 'index']);
// $routes->get('/course/lesson/(:num)/(:num)', 'CourseController::lesson/$1/$2');
// $routes->post('/course/mark-complete/(:num)', 'CourseController::markComplete/$1');
