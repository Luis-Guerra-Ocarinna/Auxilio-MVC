<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\Testimony as EntityTestimony;
use WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api {


  /**
   * Método responsável por obter a renderização dos itens de depoimentos para a página
   *
   * @param   Request $request
   * @param   Pagination $obgPagination
   * 
   * @return  string
   */
  private static function getTestimoniesItems(Request $request, &$obgPagination) {
    // DEPOIMENTOS
    $itens = [];

    // QUANTIDADE TOTAL DE REGISTRO
    $quantitadetotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

    // PÁGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['page'] ?? 1;

    // INSTÂNCIA DE PAGINAÇÃO
    $obgPagination = new Pagination($quantitadetotal, $paginaAtual, 3);

    // RESULTADOS DA PÁGINA
    $results = EntityTestimony::getTestimonies(null, 'id DESC', $obgPagination->getLimit());

    // RENDERIZA O(S) ITEM(S)
    while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
      // VIEW DA DEPOIMENTOS
      $itens[] = [
        'id'       => (int) $obTestimony->id,
        'nome'     => $obTestimony->nome,
        'mensagem' => $obTestimony->mensagem,
        'data'     => $obTestimony->data
      ];
    }

    // RETORNA OS DEPOIMENTOS
    return $itens;
  }

  /**
   * Mensagem responsável por retornar os os depoimentos cadastrados
   *
   * @param   Request  $request  
   *
   * @return  array             
   */
  public static function getTestimonies(Request $request) {
    return [
      'depoimentos' => self::getTestimoniesItems($request, $obgPagination),
      'paginacao'   => parent::getPagination($request, $obgPagination)
    ];
  }

  /**
   * Método responsável por retornar os detalhes de um depoimento
   *
   * @param   Request  $request  
   * @param   integer  $id       
   *
   * @return  array              
   */
  public static function getTestimony(Request $request, $id) {
    // VALIDA O ID DO DEPOIMENTO
    if (!is_numeric($id)) return throw new \Exception("O id '$id' não é válido", 400);

    // BUSCA DEPOIMENTO
    $obTestimony = EntityTestimony::getTestimonyById($id);

    // VALIDA SE O DEPOIMENTO EXISTE
    if (!$obTestimony instanceof EntityTestimony) throw new \Exception("O depoimento $id não foi encontrado", 404);

    // RETORNA OS DETALHES DO DEPOIMENTO
    return [
      'id'       => (int) $obTestimony->id,
      'nome'     => $obTestimony->nome,
      'mensagem' => $obTestimony->mensagem,
      'data'     => $obTestimony->data
    ];
  }
}
