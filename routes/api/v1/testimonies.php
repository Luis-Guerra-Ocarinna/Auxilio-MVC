<?php

use App\Controller\Api;
use App\Http\Request;
use App\Http\Response;

// ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies', [
  'middlewares' => [
    'api'
  ],
  function (Request $request) {
    return new Response(200, Api\Testimony::getTestimonies($request), 'application/json');
  }
]);

// ROTA DE CONSULTA INDIVIDUAL DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies/{id}', [
  'middlewares' => [
    'api'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\Testimony::getTestimony($request, $id), 'application/json');
  }
]);
