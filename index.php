<?php

require_once(__DIR__ . "\\includes\\app.php");

use App\Http\Response;
use \App\Http\Router;

// INICIA O ROUTER
$obRouter = new Router(URL);

// INCLUI AS ROTAS DE PÁGINAS
include(__DIR__ . '\\routes\\pages.php');

// INCLUI AS ROTAS DO PAINEL
include(__DIR__ . '\\routes\\admin.php');

// INCLUI AS ROTAS DO API
include(__DIR__ . '\\routes\\api.php');

# Teste: sobreposição de rotas com variaveis
$obRouter->get('/over', [
  function () {
    return new Response(200, "Teste AAAAAAAAA");
  }

]);

$obRouter->get('/{tover}', [
  function ($tover) {
    return new Response(200, "Teste $tover");
  }
]);

// IMPREME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();
