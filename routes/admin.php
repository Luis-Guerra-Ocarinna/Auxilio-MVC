<?php

use \App\Http\Response;
use \App\Controller\Admin;
use App\Http\Request;

// ROTA ADMIN
$obRouter->get('/admin', [
  'middlewares' => [
    'required-admin-login'
  ],
  function () {
    return new Response(200, 'Admin 😀');
  }
]);

// ROTA LOGIN
$obRouter->get('/admin/login', [
  'middlewares' => [
    'required-admin-logout'
  ],
  function (Request $request) {
    return new Response(200, Admin\Login::getLogin($request));
  }
]);

// ROTA LOGIN (INSERT)
$obRouter->post('/admin/login', [
  'middlewares' => [
    'required-admin-logout'
  ],
  function (Request $request) {
    return new Response(200, Admin\Login::setLogin($request));
  }
]);

// ROTA LOGOUT
$obRouter->get('/admin/logout', [
  'middlewares' => [
    'required-admin-login'
  ],
  function (Request $request) {
    return new Response(200, Admin\Login::setLogout($request));
  }
]);
