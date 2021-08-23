<?php

use App\Controller\Api;
use App\Http\Request;
use App\Http\Response;

// ROTA RAIZ DA API (V1)
$obRouter->get('/api/v1', [
  'middlewares' => [
    'api'
  ],
  function (Request $request) {
    return new Response(200, Api\Api::getDetails($request), 'application/json');
  }
]);
