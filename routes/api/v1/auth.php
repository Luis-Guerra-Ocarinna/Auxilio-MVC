<?php

use App\Controller\Api;
use App\Http\Request;
use App\Http\Response;

// ROTA DE AUTORIZAÇÂO DA API
$obRouter->post('/api/v1/auth', [
  'middlewares' => [
    'api'
  ],
  function (Request $request) {
    return new Response(201, Api\Auth::generateToken($request), 'application/json');
  }
]);
