<?php

use App\Controller\Api;
use App\Http\Request;
use App\Http\Response;

// ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies', [
  'middlewares' => [
    'api',
    'cache'
  ],
  function (Request $request) {
    return new Response(200, Api\Testimony::getTestimonies($request), 'application/json');
  }
]);

// ROTA DE CONSULTA INDIVIDUAL DE DEPOIMENTOS
$obRouter->get('/api/v1/testimonies/{id}', [
  'middlewares' => [
    'api',
    'cache'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\Testimony::getTestimony($request, $id), 'application/json');
  }
]);

// ROTA DE CADASTRO DE DEPOIMENTOS
$obRouter->post('/api/v1/testimonies', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request) {
    return new Response(201, Api\Testimony::setNewTestimony($request), 'application/json');
  }
]);

// ROTA DE ATULIZAÇÃO DE DEPOIMENTOS
$obRouter->put('/api/v1/testimonies/{id}', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\Testimony::setEditTestimony($request, $id), 'application/json');
  }
]);

// ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->delete('/api/v1/testimonies/{id}', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\Testimony::setDeleteTestimony($request, $id), 'application/json');
  }
]);
