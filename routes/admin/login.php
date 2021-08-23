<?php

use App\Controller\Admin;
use App\Http\Request;
use App\Http\Response;

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
