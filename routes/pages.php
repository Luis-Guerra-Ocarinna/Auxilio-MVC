<?php

use App\Http\Response;
use App\Controller\Pages;

// ROTA HOME
$obRouter->get('', [
  function () {
    return new Response(200, Pages\Home::getHome());
  }
]);

// ROTA SOBRE
$obRouter->get('/sobre', [
  function () {
    return new Response(200, Pages\About::getAbout());
  }
]);

// /*
// ROTA DINÂMICA
$obRouter->get('/pagina/{idPagina}/{acao}', [
  function ($idPagina, $acao) {
    return new Response(200, 'Página ' . $idPagina . ' - ' .  $acao . "
    <br> <a id='inicio'>Salve</a>
    <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> <br> <br> <hr> 
    <input type='button' value='Retornar' onclick='location.href = \"#inicio\";'/>
    ");
  }
]);
// */

// ROTA DEPOIMENTOS
$obRouter->get('/depoimentos', [
  function ($request) {
    return new Response(200, Pages\Testimony::getTestimonies($request));
  }
]);

// ROTA DEPOIMENTOS (INSERT)
$obRouter->post('/depoimentos', [
  function ($request) {
    return new Response(200, Pages\Testimony::insertTestimmony($request));
  }
]);
