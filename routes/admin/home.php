<?php

use App\Http\Response;
use App\Controller\Admin;
use App\Http\Request;

// ROTA ADMIN
$obRouter->get('/admin', [
  'middlewares' => [
    'required-admin-login'
  ],
  function (Request $request) {
    return new Response(200, Admin\Home::getHome($request));
  }
]);
