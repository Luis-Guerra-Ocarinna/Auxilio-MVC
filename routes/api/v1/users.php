<?php

use App\Controller\Api;
use App\Http\Request;
use App\Http\Response;

// ROTA DE LISTAGEM DE USUÁRIOS
$obRouter->get('/api/v1/users', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request) {
    return new Response(200, Api\User::getUsers($request), 'application/json');
  }
]);

// ROTA DE CONSULTA INDIVIDUAL DE USUÁRIOS
$obRouter->get('/api/v1/users/{id}', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\User::getUser($request, $id), 'application/json');
  }
]);

// ROTA DE CADASTRO DE USUÁRIOS
$obRouter->post('/api/v1/users', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request) {
    return new Response(201, Api\User::setNewUser($request), 'application/json');
  }
]);

// ROTA DE ATULIZAÇÃO DE USUÁRIOS
$obRouter->put('/api/v1/users/{id}', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\User::setEditUser($request, $id), 'application/json');
  }
]);

// ROTA DE EXCLUSÃO DE USUÁRIOS
$obRouter->delete('/api/v1/users/{id}', [
  'middlewares' => [
    'api',
    'user-basic-auth'
  ],
  function (Request $request, $id) {
    return new Response(200, Api\User::setDeleteUser($request, $id), 'application/json');
  }
]);
