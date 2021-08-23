<?php

namespace App\Controller\Api;

use App\Http\Request;
use WilliamCosta\DatabaseManager\Pagination;

class Api {

  /**
   * Mensagem responsável por retornar os detalhes da API
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function getDetails(Request $request) {
    return [
      'nome'      => 'API — HELP',
      'versao'    => 'v1.0.0',
      'autor'     => 'Luis Guerra',
      'professor' => 'William Costa',
      'email'     => 'luisguerra2004@gmail.com'
    ];
  }

  /**
   * Método responsável por retornar os detalhes da pagina
   *
   * @param   Request     $request        
   * @param   Pagination  $obgPagination  
   *
   * @return  array                      
   */
  protected static function getPagination(Request $request, Pagination $obgPagination) {
    // QUERY PARAMS
    $queryParams = $request->getQueryParams();


    // PÁGINAS
    $pages = $obgPagination->getPages();

    // RETORNA
    return [
      'paginaAtual'       => isset($queryParams['page']) ? (int) $queryParams['page'] : 1,
      'quantitadePaginas' => !empty($pages) ? count($pages) : 1
    ];
  }
}
