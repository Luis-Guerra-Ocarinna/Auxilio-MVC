<?php

require_once(__DIR__ . "\\includes\\app.php");

use \App\Http\Router;

// INICIA O ROUTER
$obRouter = new Router(URL);

// INCLUI AS ROTAS DE PÁGINAS
include(__DIR__ . '\\routes\\pages.php');

// IMPREME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();
